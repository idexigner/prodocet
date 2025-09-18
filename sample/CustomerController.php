<?php

namespace App\Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Resources\CustomerResource;
use App\Modules\Accounts\Repositories\Interfaces\CustomerInterface;
use App\Modules\Accounts\Repositories\Interfaces\ChartOfAccountInterface;
use App\Modules\Accounts\Repositories\Interfaces\CoaModuleInterface;
use App\Modules\Accounts\Repositories\Interfaces\ChartOfAccountMappingInterface;
use App\Modules\Accounts\Repositories\Interfaces\CustomerCategoryInterface;
use App\Modules\Accounts\Repositories\Interfaces\CustomerFloorInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    use ApiResponse;

    protected $customerRepository;
    protected $chartOfAccountRepository;
    protected $coaModuleRepository;
    protected $mappingRepository;
    protected $customerCategoryRepository;
    protected $customerFloorRepository;

    public function __construct(
        CustomerInterface $customerRepository,
        ChartOfAccountInterface $chartOfAccountRepository,
        CoaModuleInterface $coaModuleRepository,
        ChartOfAccountMappingInterface $mappingRepository,
        CustomerCategoryInterface $customerCategoryRepository,
        CustomerFloorInterface $customerFloorRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->chartOfAccountRepository = $chartOfAccountRepository;
        $this->coaModuleRepository = $coaModuleRepository;
        $this->mappingRepository = $mappingRepository;
        $this->customerCategoryRepository = $customerCategoryRepository;
        $this->customerFloorRepository = $customerFloorRepository;
    }

    public function getHeads()
    {
        // First check if customer module has a synced head
        $customerModule = $this->coaModuleRepository->getModel()
            ->where('name', 'customer')
            ->first();

        if ($customerModule && $customerModule->synced_head_id) {
            // If synced head exists, return only that head
            return $this->chartOfAccountRepository->getModel()
                ->where('id', $customerModule->synced_head_id)
                ->active()
                ->get();
        }
       
        // If no synced head, return all level 4 heads
        return $this->chartOfAccountRepository->getModel()
            ->byLevel(4)
            ->where('is_transactional_level', false)
            ->active()
            ->get();
    }

    public function index(Request $request)
    {
        if (!_has_permission('customers.read')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        if ($request->ajax()) {
            $filters = [
                'filter_head' => $request->filter_head,
                'filter_category' => $request->filter_category,
                'filter_floor' => $request->filter_floor,
                'filter_status' => $request->filter_status,
                'filter_search' => $request->filter_search
            ];

            $customers = $this->customerRepository->getFilteredCustomers($filters);
            
            return DataTables::of($customers)
                ->addColumn('encrypted_id', function($customer) {
                    return _encrypt_id($customer->id);
                })
                ->addColumn('chart_of_account_name', function($customer) {
                    return $customer->chartOfAccount?->name;
                })
                ->addColumn('chart_of_account_code', function($customer) {
                    return $customer->chartOfAccount?->code;
                })
                ->addColumn('parent_account_name', function($customer) {
                    return $customer->chartOfAccount?->parent?->name;
                })
                ->addColumn('parent_account_code', function($customer) {
                    return $customer->chartOfAccount?->parent?->code;
                })
                ->addColumn('customer_category_name', function($customer) {
                    return $customer->customerCategory?->name;
                })
                ->addColumn('customer_floor_name', function($customer) {
                    return $customer->customerFloor?->name;
                })
                ->addColumn('can_edit', function($customer) {
                    return _has_permission('customers.edit');
                })
                ->addColumn('can_delete', function($customer) {
                    return _has_permission('customers.delete');
                })
                ->make(true);
        }

        // Get categories and floors for the view
        $categories = $this->customerCategoryRepository->getModel()->active()->get();
        $floors = $this->customerFloorRepository->getModel()->active()->get();

        return view('admin.accounts.customers', compact('categories', 'floors'));
    }

    public function create(Request $request)
    {
        if (!_has_permission('customers.create')) {
            return $this->error('Access denied', 403);
        }

        $heads = $this->getHeads();
        $categories = $this->customerCategoryRepository->getModel()->active()->get();
        $floors = $this->customerFloorRepository->getModel()->active()->get();

        return $this->success([
            'heads' => $heads,
            'categories' => $categories,
            'floors' => $floors
        ]);
    }

    public function store(Request $request)
    {
        if (!_has_permission('customers.create')) {
            return $this->error('Access denied', 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ntn' => 'nullable|string|max:255',
            'strn' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'sqft' => 'nullable|numeric',
            'credit_terms' => 'nullable|integer',
            'cell' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'status' => 'required|in:active,inactive',
            'customer_category_id' => 'nullable|exists:customer_categories,id',
            'customer_floor_id' => 'nullable|exists:customer_floors,id',
            'notes' => 'nullable|string',
            'opening_date' => 'nullable|date',
            'unit_value_for_rent' => 'nullable|numeric',
            'unit_value_for_rent_percent' => 'nullable|numeric',
            'minimum_rent' => 'nullable|numeric',
            'hvac_billing_by' => 'nullable|in:area,use',
            'hvac_tons' => 'nullable|numeric',
            'hvac_hour_rate' => 'nullable|numeric',
            'operation_hours' => 'nullable|numeric',
            'shop_nos' => 'nullable|string',
            'gas_meter_id' => 'nullable|string',
            'gas_rate' => 'nullable|numeric',
            'gas_last_reading' => 'nullable|numeric',
            'post_paid_electric' => 'nullable|boolean',
            'hvac' => 'nullable|boolean',
            'maintenance' => 'nullable|boolean',
            'gas' => 'nullable|boolean',
            'electric_unit_rate' => 'nullable|numeric',
            'genset_unit_rate' => 'nullable|numeric',
            'billing_areas.basement.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.basement.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.basement.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.rent_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.rent_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.rent_rate_per_sqft' => 'nullable|numeric',
            'electric_meters' => 'nullable|array',
            'electric_meters.*.meter_id' => 'nullable|string',
            'electric_meters.*.last_reading_electric' => 'nullable|numeric',
            'electric_meters.*.last_reading_genset' => 'nullable|numeric',
            'electric_meters.*.bill_type' => 'nullable|in:prepaid,postpaid',
            'customer_notes' => 'nullable|array',
            'customer_notes.*.note_date' => 'nullable|date',
            'customer_notes.*.note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // Get the parent chart of account (level 4)
        $parentAccount = $this->chartOfAccountRepository->find($request->chart_of_account_id);
        if (!$parentAccount) {
            return $this->error('Parent account not found', 404);
        }

        // Generate the next code for level 5
        $nextCode = $this->chartOfAccountRepository->getNextCode($parentAccount->id, 5);

        // Create the new chart of account at level 5
        $chartOfAccountData = [
            'name' => $request->name,
            'code' => $nextCode,
            'parent_id' => $parentAccount->id,
            'level' => 5,
            'is_transactional_level' => true,
            'status' => $request->status
        ];

        $newChartOfAccount = $this->chartOfAccountRepository->create($chartOfAccountData);

        // Create the customer with the new chart of account ID
        $customerData = $request->all();
        $customerData['chart_of_account_id'] = $newChartOfAccount->id;
        
        // Handle boolean fields
        $customerData['post_paid_electric'] = $request->has('post_paid_electric') ? 1 : 0;
        $customerData['hvac'] = $request->has('hvac') ? 1 : 0;
        $customerData['maintenance'] = $request->has('maintenance') ? 1 : 0;
        $customerData['gas'] = $request->has('gas') ? 1 : 0;
        $customerData['rent'] = $request->has('rent') ? 1 : 0;
        
        $customer = $this->customerRepository->create($customerData);

        // Create billing areas
        if ($request->has('billing_areas') && is_array($request->billing_areas)) {
            $billingAreasData = $request->billing_areas;
            foreach (['basement', 'mezzanine', 'shop'] as $areaType) {
                if (isset($billingAreasData[$areaType]) && is_array($billingAreasData[$areaType])) {
                    $areaData = $billingAreasData[$areaType];
                    // Only create if at least one field has a value
                    if (!empty($areaData['hvac_area_sqft']) || !empty($areaData['hvac_rate_per_sqft']) || 
                        !empty($areaData['maintenance_area_sqft']) || !empty($areaData['maintenance_rate_per_sqft']) ||
                        !empty($areaData['rent_area_sqft']) || !empty($areaData['rent_rate_per_sqft'])) {
                        
                        $areaData['customer_id'] = $customer->id;
                        $areaData['area_type'] = $areaType;
                        $customer->billingAreas()->create($areaData);
                    }
                }
            }
        }

        // Create electric meters
        if ($request->has('electric_meters') && is_array($request->electric_meters)) {
            foreach ($request->electric_meters as $meter) {
                if (!empty($meter['meter_id'])) {
                    $meter['customer_id'] = $customer->id;
                    $customer->electricMeters()->create($meter);
                }
            }
        }

        // Create customer notes
        if ($request->has('customer_notes') && is_array($request->customer_notes)) {
            foreach ($request->customer_notes as $note) {
                if (!empty($note['note'])) {
                    $note['customer_id'] = $customer->id;
                    $customer->notes()->create($note);
                }
            }
        }

        // Find the customer module
        $module = $this->coaModuleRepository->getModel()
            ->where('name', 'customer')
            ->first();

        if ($module) {
            // Create mapping
            $this->mappingRepository->create([
                'module_id' => $module->id,
                'chart_of_account_id' => $newChartOfAccount->id,
                'mapping_module' => $module->name
            ]);
        }

        return $this->success(new CustomerResource($customer), 'Customer created successfully', 201);
    }

    public function edit($encryptedId)
    {
        if (!_has_permission('customers.edit')) {
            return $this->error('Access denied', 403);
        }

        $id = _decrypt_id($encryptedId);
        if (!$id) {
            return $this->error('Invalid customer ID', 400);
        }

        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return $this->notFound('Customer not found');
        }

        $heads = $this->getHeads();
        $categories = $this->customerCategoryRepository->getModel()->active()->get();
        $floors = $this->customerFloorRepository->getModel()->active()->get();

        return $this->success([
            'customer' => new CustomerResource($customer),
            'heads' => $heads,
            'categories' => $categories,
            'floors' => $floors
        ]);
    }

    public function update(Request $request)
    {
        if (!_has_permission('customers.update')) {
            return $this->error('Access denied', 403);
        }

        $id = _decrypt_id($request->id);
        if (!$id) {
            return $this->error('Invalid customer ID', 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ntn' => 'nullable|string|max:255',
            'strn' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'sqft' => 'nullable|numeric',
            'credit_terms' => 'nullable|integer',
            'cell' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
            'customer_category_id' => 'nullable|exists:customer_categories,id',
            'customer_floor_id' => 'nullable|exists:customer_floors,id',
            'notes' => 'nullable|string',
            'opening_date' => 'nullable|date',
            'unit_value_for_rent' => 'nullable|numeric',
            'unit_value_for_rent_percent' => 'nullable|numeric',
            'minimum_rent' => 'nullable|numeric',
            'hvac_billing_by' => 'nullable|in:area,use',
            'hvac_tons' => 'nullable|numeric',
            'hvac_hour_rate' => 'nullable|numeric',
            'operation_hours' => 'nullable|numeric',
            'shop_nos' => 'nullable|string',
            'gas_meter_id' => 'nullable|string',
            'gas_rate' => 'nullable|numeric',
            'gas_last_reading' => 'nullable|numeric',
            'post_paid_electric' => 'nullable|boolean',
            'hvac' => 'nullable|boolean',
            'maintenance' => 'nullable|boolean',
            'gas' => 'nullable|boolean',
            'electric_unit_rate' => 'nullable|numeric',
            'genset_unit_rate' => 'nullable|numeric',
            'billing_areas.basement.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.basement.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.basement.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.basement.rent_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.mezzanine.rent_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.hvac_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.hvac_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.maintenance_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.maintenance_rate_per_sqft' => 'nullable|numeric',
            'billing_areas.shop.rent_area_sqft' => 'nullable|numeric',
            'billing_areas.shop.rent_rate_per_sqft' => 'nullable|numeric',
            'electric_meters' => 'nullable|array',
            'electric_meters.*.meter_id' => 'nullable|string',
            'electric_meters.*.last_reading_electric' => 'nullable|numeric',
            'electric_meters.*.last_reading_genset' => 'nullable|numeric',
            'electric_meters.*.bill_type' => 'nullable|in:prepaid,postpaid',
            'customer_notes' => 'nullable|array',
            'customer_notes.*.note_date' => 'nullable|date',
            'customer_notes.*.note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // Get the customer and its associated chart of account
        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return $this->notFound('Customer not found');
        }

        // Update the chart of account name and status
        $this->chartOfAccountRepository->update($customer->chart_of_account_id, [
            'name' => $request->name,
            'status' => $request->status
        ]);

        // Update the customer
        $customerData = $request->except(['chart_of_account_id']);
        
        // Handle boolean fields
        $customerData['post_paid_electric'] = $request->has('post_paid_electric') ? 1 : 0;
        $customerData['hvac'] = $request->has('hvac') ? 1 : 0;
        $customerData['maintenance'] = $request->has('maintenance') ? 1 : 0;
        $customerData['gas'] = $request->has('gas') ? 1 : 0;
        $customerData['rent'] = $request->has('rent') ? 1 : 0;
        
        $customer = $this->customerRepository->update($id, $customerData);

        // Update billing areas
        if ($request->has('billing_areas') && is_array($request->billing_areas)) {
            // Delete existing billing areas
            $customer->billingAreas()->delete();
            
            // Create new billing areas
            $billingAreasData = $request->billing_areas;
            foreach (['basement', 'mezzanine', 'shop'] as $areaType) {
                if (isset($billingAreasData[$areaType]) && is_array($billingAreasData[$areaType])) {
                    $areaData = $billingAreasData[$areaType];
                    // Only create if at least one field has a value
                    if (!empty($areaData['hvac_area_sqft']) || !empty($areaData['hvac_rate_per_sqft']) || 
                        !empty($areaData['maintenance_area_sqft']) || !empty($areaData['maintenance_rate_per_sqft']) ||
                        !empty($areaData['rent_area_sqft']) || !empty($areaData['rent_rate_per_sqft'])) {
                        
                        $areaData['customer_id'] = $customer->id;
                        $areaData['area_type'] = $areaType;
                        $customer->billingAreas()->create($areaData);
                    }
                }
            }
        }

        // Update electric meters
        if ($request->has('electric_meters') && is_array($request->electric_meters)) {
            // Delete existing electric meters
            $customer->electricMeters()->delete();
            
            // Create new electric meters
            foreach ($request->electric_meters as $meter) {
                if (!empty($meter['meter_id'])) {
                    $meter['customer_id'] = $customer->id;
                    $customer->electricMeters()->create($meter);
                }
            }
        }

        // Update customer notes
        if ($request->has('customer_notes') && is_array($request->customer_notes)) {
            // Delete existing notes
            $customer->notes()->delete();
            
            // Create new notes
            foreach ($request->customer_notes as $note) {
                if (!empty($note['note'])) {
                    $note['customer_id'] = $customer->id;
                    $customer->notes()->create($note);
                }
            }
        }

        return $this->success(new CustomerResource($customer), 'Customer updated successfully');
    }

    public function destroy($encryptedId)
    {
        if (!_has_permission('customers.delete')) {
            return $this->error('Access denied', 403);
        }

        $id = _decrypt_id($encryptedId);
        if (!$id) {
            return $this->error('Invalid customer ID', 400);
        }

        // Get the customer and its associated chart of account
        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return $this->notFound('Customer not found');
        }

        // Delete the associated chart of account
        $this->chartOfAccountRepository->delete($customer->chart_of_account_id);

        // Delete the customer
        if (!$this->customerRepository->delete($id)) {
            return $this->notFound('Customer not found');
        }

        return $this->success(null, 'Customer and associated account deleted successfully');
    }
} 