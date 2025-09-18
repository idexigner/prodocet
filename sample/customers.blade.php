@extends('admin.layouts.app')

@push('title') Customers @endpush

@push('css-link')
    @include('partials.common.datatable_style')


    <style>

    /* .custom-pab-modal .modal-dialog {
        max-width: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
    } */

    </style>
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('customers.read'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>Customers</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('customers.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        Add New Customer
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">Head (COA)</label>
                                    <select class="form-select" id="filter_head">
                                        <option value="">All Heads</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" id="filter_category">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Floor</label>
                                    <select class="form-select" id="filter_floor">
                                        <option value="">All Floors</option>
                                        @foreach($floors as $floor)
                                            <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="filter_status">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" id="filter_search" placeholder="Search...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="apply_filters">Apply Filters</button>
                                </div>
                            </div>
                            <table id="table-standard" class="table table-bordered table-striped">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">Access Denied!</h2>
                <p class="mb-4 mx-2">You don't have permission to access customers.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    @endif

    @if(_has_permission('customers.create') || _has_permission('customers.edit'))
        <!-- Add/Edit Customer Modal -->
        <div class="modal fade custom-pab-modal" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">Contact Information</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing" type="button" role="tab">Billing Information</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="electric-tab" data-bs-toggle="tab" data-bs-target="#electric" type="button" role="tab">Electric Bill Setup</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="customerTabsContent">
                                <!-- Contact Information Tab -->
                                <div class="tab-pane fade show active" id="contact" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Head (COA) <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="chart_of_account_id" class="form-select" required>
                                                        <option value="">Select Head</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Customer Name <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="name" placeholder="Customer Name" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Category</label>
                                                <div class="col-sm-8">
                                                                                        <select name="customer_category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Floor</label>
                                                <div class="col-sm-8">
                                                                                        <select name="customer_floor_id" class="form-select">
                                        <option value="">Select Floor</option>
                                        @foreach($floors as $floor)
                                            <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                        @endforeach
                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">NTN#</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="ntn" placeholder="NTN Number" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">STRN#</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="strn" placeholder="STRN Number" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Address</label>
                                                <div class="col-sm-8">
                                                    <textarea name="address" placeholder="Address" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Email</label>
                                                <div class="col-sm-8">
                                                    <input type="email" name="email" placeholder="Email" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Phone</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="phone" placeholder="Phone" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Cell</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="cell" placeholder="Cell" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">SQFT</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="sqft" placeholder="SQFT" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Credit Terms</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="credit_terms" placeholder="Credit Terms" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Credit Limit</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="credit_limit" placeholder="Credit Limit" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select name="status" class="form-select" required>
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Notes</label>
                                                <div class="col-sm-8">
                                                    <textarea name="notes" placeholder="Notes" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Billing Information Tab -->
                                <div class="tab-pane fade" id="billing" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Opening Date</label>
                                                <div class="col-sm-8">
                                                    <input type="date" name="opening_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Unit Value for Rent</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="unit_value_for_rent" placeholder="Unit Value for Rent" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Unit Value %</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="unit_value_for_rent_percent" placeholder="Unit Value %" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Minimum Rent</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="minimum_rent" placeholder="Minimum Rent" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">HVAC Billing By</label>
                                                <div class="col-sm-8">
                                                    <select name="hvac_billing_by" class="form-select">
                                                        <option value="area">Area</option>
                                                        <option value="use">Use</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">HVAC Tons</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="hvac_tons" placeholder="HVAC Tons" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">HVAC Hour Rate</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="hvac_hour_rate" placeholder="HVAC Hour Rate" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Operation Hours</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="operation_hours" placeholder="Operation Hours" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Shop Numbers</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="shop_nos" placeholder="Shop Numbers" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Gas Meter ID</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="gas_meter_id" placeholder="Gas Meter ID" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Gas Rate</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="gas_rate" placeholder="Gas Rate" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Gas Last Reading</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="gas_last_reading" placeholder="Gas Last Reading" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Services</label>
                                                <div class="col-sm-8">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="post_paid_electric" value="1">
                                                        <label class="form-check-label">Post Paid Electric</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="post_paid_genset" value="1">
                                                        <label class="form-check-label">Post Paid Genset</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="hvac" value="1">
                                                        <label class="form-check-label">HVAC</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="maintenance" value="1">
                                                        <label class="form-check-label">Maintenance</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="gas" value="1">
                                                        <label class="form-check-label">Gas</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="rent" value="1">
                                                        <label class="form-check-label">Rent</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tax Information Section -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6>Tax Information</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Tax WHT %</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="tax_wht_percent" placeholder="Tax WHT Percentage" class="form-control" step="0.01" min="0" max="100" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Tax SRB %</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="tax_srb_percent" placeholder="Tax SRB Percentage" class="form-control" step="0.01" min="0" max="100" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Services Section -->
                                 

                                    <!-- Billing Areas Table -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6>Billing Areas</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Area Type</th>
                                                            <th>HVAC Area (sq.ft)</th>
                                                            <th>HVAC Rate (per sq.ft)</th>
                                                            <th>Maintenance Area (sq.ft)</th>
                                                            <th>Maintenance Rate (per sq.ft)</th>
                                                            <th>Rent Area (sq.ft)</th>
                                                            <th>Rent Rate (per sq.ft)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Basement</td>
                                                            <td><input type="number" name="billing_areas[basement][hvac_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[basement][hvac_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[basement][maintenance_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[basement][maintenance_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[basement][rent_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[basement][rent_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Mezzanine</td>
                                                            <td><input type="number" name="billing_areas[mezzanine][hvac_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[mezzanine][hvac_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[mezzanine][maintenance_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[mezzanine][maintenance_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[mezzanine][rent_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[mezzanine][rent_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Shop</td>
                                                            <td><input type="number" name="billing_areas[shop][hvac_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[shop][hvac_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[shop][maintenance_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[shop][maintenance_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[shop][rent_area_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                            <td><input type="number" name="billing_areas[shop][rent_rate_per_sqft]" class="form-control form-control-sm" step="0.01"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Electric Bill Setup Tab -->
                                <div class="tab-pane fade" id="electric" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Electric Unit Rate</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="electric_unit_rate" placeholder="Electric Unit Rate" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">Genset Unit Rate</label>
                                                <div class="col-sm-8">
                                                    <input type="number" name="genset_unit_rate" placeholder="Genset Unit Rate" class="form-control" step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Electric Meters -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6>Electric Meters</h6>
                                                <button type="button" class="btn btn-sm btn-primary" id="add-meter">Add Meter</button>
                                            </div>
                                            <div id="meters-container">
                                                <!-- Meters will be added here dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Tab -->
                                <div class="tab-pane fade" id="notes" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6>Customer Notes</h6>
                                                <button type="button" class="btn btn-sm btn-primary" id="add-note">Add Note</button>
                                            </div>
                                            <div id="notes-container">
                                                <!-- Notes will be added here dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                                 <div class="modal-footer">
                             <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                             <button type="button" data-url="{{ route('customers.store') }}" id="create_form_btn" class="btn btn-primary">Submit</button>
                             <button type="button" data-url="{{ route('customers.update') }}" id="update_form_btn" class="btn btn-primary">Update</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js-link')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table-standard');
            var tableName = '#table-standard';
            var ajaxUrl = "{{ route('customers.index') }}";

            var columnsArray = [
                { data: 'name', name: 'name', title: 'Name' },
                { data: 'customer_category.name', name: 'customer_category.name', title: 'Category' },
                { data: 'customer_floor.name', name: 'customer_floor.name', title: 'Floor' },
                { data: 'phone', name: 'phone', title: 'Phone' },
                { data: 'email', name: 'email', title: 'Email' },
                {
                    data: 'status',
                    name: 'status',
                    title: 'Status',
                    render: function(data) {
                        return data === 'active' 
                            ? '<span class="badge bg-primary">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    render: function(data, type, row) {
                        var actions = '';
                        if (row.can_edit) {
                            actions += '<a href="#" class="edit" data-id="'+row.encrypted_id+'"><i class="icon-base ti ti-pencil"></i></a> ';
                        }
                        if (row.can_delete) {
                            actions += '<a href="#" class="text-danger delete" data-id="'+row.encrypted_id+'"><i class="icon-base ti ti-trash"></i></a>';
                        }
                        return actions || '<span class="text-muted">No actions</span>';
                    },
                    orderable: false,
                    searchable: false
                }
            ];

            // Initialize DataTable with filters
            var dataTable = initializeDataTable(tableName, ajaxUrl, columnsArray, {
                filterSelectors: [
                    '#filter_head',
                    '#filter_category',
                    '#filter_floor',
                    '#filter_status',
                    '#filter_search'
                ]
            });

            // Load dropdown data
            loadChartOfAccounts();

            // Apply filters when button is clicked
            $('#apply_filters').on('click', function() {
                dataTable.ajax.reload();
            });

            // handle click event for "Add" button
            $('.add_new').on('click', function(){
                $("#create_form_btn").show();
                $("#update_form_btn").hide();
                $("#modal_create_form .modal-title").text("Add New Customer");
                $("#create-form")[0].reset();
                clearDynamicContent();
                $("#modal_create_form").modal('show');
            });

            // For Creating and updating the record
            $('#create_form_btn, #update_form_btn').on('click', function() {
                var form = $('#create-form')[0];
                console.log("form", form);
                
                // Check for HTML5 validation errors before AJAX request
                if (!form.checkValidity()) {
                    
                    // Find the first invalid element
                    var firstInvalidElement = form.querySelector(':invalid');
                    if (firstInvalidElement) {
                        // Switch to the tab containing the first error
                        switchToTabWithField($(firstInvalidElement));
                        
                        // Focus on the invalid field
                        firstInvalidElement.focus();
                        
                        // Show validation message
                        firstInvalidElement.reportValidity();
                    }
                    return false;
                }
                
                
                var url = $(this).data('url');
                var type = "POST";
                var form_data = new FormData(form);

                _ajaxRequest(url, type, form_data, function(response) {
                    dataTable.ajax.reload();
                    $("#modal_create_form").modal('hide');
                    _handleSuccess(response.message);
                });
            });
            
            // Function to switch to the tab containing a specific field
            function switchToTabWithField(fieldElement) {
                
                var fieldName = fieldElement.attr('name');
                var tabId = getFieldTabId(fieldName);
                
                if (tabId) {
                    // Switch to the tab
                    $('[data-bs-target="' + tabId + '"]').tab('show');
                }
            }
            
            // Function to determine which tab a field belongs to
            function getFieldTabId(fieldName) {
                var fieldMappings = {
                    // Contact Information Tab
                    'chart_of_account_id': '#contact',
                    'name': '#contact',
                    'customer_category_id': '#contact',
                    'customer_floor_id': '#contact',
                    'ntn': '#contact',
                    'strn': '#contact',
                    'address': '#contact',
                    'email': '#contact',
                    'phone': '#contact',
                    'cell': '#contact',
                    'sqft': '#contact',
                    'credit_terms': '#contact',
                    'credit_limit': '#contact',
                    'status': '#contact',
                    'notes': '#contact',
                    
                    // Billing Information Tab
                    'opening_date': '#billing',
                    'unit_value_for_rent': '#billing',
                    'unit_value_for_rent_percent': '#billing',
                    'minimum_rent': '#billing',
                    'hvac_billing_by': '#billing',
                    'hvac_tons': '#billing',
                    'hvac_hour_rate': '#billing',
                    'operation_hours': '#billing',
                    'shop_nos': '#billing',
                    'gas_meter_id': '#billing',
                    'gas_rate': '#billing',
                    'gas_last_reading': '#billing',
                    'post_paid_electric': '#billing',
                    'post_paid_genset': '#billing',
                    'hvac': '#billing',
                    'maintenance': '#billing',
                    'gas': '#billing',
                    'rent': '#billing',
                    'tax_wht_percent': '#billing',
                    'tax_srb_percent': '#billing',

                    
                    // Electric Bill Setup Tab
                    'electric_unit_rate': '#electric',
                    'genset_unit_rate': '#electric'
                };
                
                return fieldMappings[fieldName] || '#contact';
            }

            // handle click event for "Edit" button
            table.on('click', '.edit', function() {
                $("#modal_create_form .modal-title").text("Update Customer");
                var id = $(this).data('id');
                var url = "{{ route('customers.edit', ':id') }}";
                url = url.replace(':id', id);

                _ajaxRequest(url, 'GET', null, function(response) {
                    var data = response.data.customer;
                    
                    // Fill basic form fields
                    $("input[name=id]").val(data.encrypted_id);
                    // Set chart_of_account_id to first option by default when editing
                    $("select[name=chart_of_account_id] option:not([value='']):first").prop('selected', true);
                    $("input[name=name]").val(data.name);
                    $("select[name=customer_category_id]").val(data.customer_category_id);
                    $("select[name=customer_floor_id]").val(data.customer_floor_id);
                    $("input[name=ntn]").val(data.ntn);
                    $("input[name=strn]").val(data.strn);
                    $("textarea[name=address]").val(data.address);
                    $("input[name=email]").val(data.email);
                    $("input[name=phone]").val(data.phone);
                    $("input[name=cell]").val(data.cell);
                    $("input[name=sqft]").val(data.sqft);
                    $("input[name=credit_terms]").val(data.credit_terms);
                    $("input[name=credit_limit]").val(data.credit_limit);
                    $("select[name=status]").val(data.status);
                    $("textarea[name=notes]").val(data.notes);

                    // Fill billing information
                    $("input[name=opening_date]").val(data.opening_date);
                    $("input[name=unit_value_for_rent]").val(data.unit_value_for_rent);
                    $("input[name=unit_value_for_rent_percent]").val(data.unit_value_for_rent_percent);
                    $("input[name=minimum_rent]").val(data.minimum_rent);
                    $("select[name=hvac_billing_by]").val(data.hvac_billing_by);
                    $("input[name=hvac_tons]").val(data.hvac_tons);
                    $("input[name=hvac_hour_rate]").val(data.hvac_hour_rate);
                    $("input[name=operation_hours]").val(data.operation_hours);
                    $("input[name=shop_nos]").val(data.shop_nos);
                    $("input[name=gas_meter_id]").val(data.gas_meter_id);
                    $("input[name=gas_rate]").val(data.gas_rate);
                    $("input[name=gas_last_reading]").val(data.gas_last_reading);
                    $("input[name=electric_unit_rate]").val(data.electric_unit_rate);
                    $("input[name=genset_unit_rate]").val(data.genset_unit_rate);
                    $("input[name=tax_wht_percent]").val(data.tax_wht_percent);
                    $("input[name=tax_srb_percent]").val(data.tax_srb_percent);
                    // Set checkboxes
                    $("input[name=post_paid_electric]").prop('checked', data.post_paid_electric);
                    $("input[name=post_paid_genset]").prop('checked', data.post_paid_genset);

                    $("input[name=hvac]").prop('checked', data.hvac);
                    $("input[name=maintenance]").prop('checked', data.maintenance);
                    $("input[name=gas]").prop('checked', data.gas);
                    $("input[name=rent]").prop('checked', data.rent);
                    // Load related data
                    loadBillingAreas(data.billing_areas);
                    loadElectricMeters(data.electric_meters);
                    loadCustomerNotes(data.notes_list);
                    
                    $("#modal_create_form").modal('show');
                    $("#create_form_btn").hide();
                    $("#update_form_btn").show();
                });
            });

            // handle click event for "Delete" button
            table.on('click', '.delete', function() {
                var id = $(this).data('id');
                var currentPage = table.DataTable().page.info().page;
                var url = "{{ route('customers.destroy', ':id') }}".replace(':id', id);

                _confirmDelete({
                    deleteUrl: url,
                    deleteData: { "_token": "{{ csrf_token() }}" },
                    table: table,
                    currentPage: currentPage,
                    successCallback: function(response) {
                        dataTable.ajax.reload();
                        _handleSuccess("Customer deleted successfully");
                    }
                });
            });

            // Add meter functionality
            $('#add-meter').on('click', function() {
                addMeterRow();
            });

            // Add note functionality
            $('#add-note').on('click', function() {
                addNoteRow();
            });

            // Functions to load dropdown data
            function loadChartOfAccounts() {
                // $.get("{{ route('chart-of-accounts.parent-accounts') }}", function(response) {
                $.get("{{ route('customers.create') }}", function(response) {
                    console.log("response", response.data);
                    var options = '<option value="">Select Head</option>';
                    if (response.data.heads && response.data.heads.length > 0) {
                        response.data.heads.forEach(function(item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                    }
                    $('select[name=chart_of_account_id]').html(options);
                    $('#filter_head').html(options);
                }).fail(function() {
                    console.log('Failed to load chart of accounts');
                });
            }

            function addMeterRow() {
                var meterIndex = $('#meters-container .meter-row').length;
                var meterHtml = '<div class="meter-row border p-3 mb-3">' +
                    '<div class="row">' +
                        '<div class="col-md-3">' +
                            '<label class="form-label">Meter ID</label>' +
                            '<input type="text" name="electric_meters[' + meterIndex + '][meter_id]" class="form-control" required>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<label class="form-label">Last Reading Electric</label>' +
                            '<input type="number" name="electric_meters[' + meterIndex + '][last_reading_electric]" class="form-control" step="0.01">' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<label class="form-label">Last Reading Genset</label>' +
                            '<input type="number" name="electric_meters[' + meterIndex + '][last_reading_genset]" class="form-control" step="0.01">' +
                        '</div>' +
                        '<div class="col-md-2">' +
                            '<label class="form-label">Bill Type</label>' +
                            '<select name="electric_meters[' + meterIndex + '][bill_type]" class="form-select">' +
                                '<option value="postpaid">Postpaid</option>' +
                                '<option value="prepaid">Prepaid</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-md-1">' +
                            '<label class="form-label">&nbsp;</label>' +
                            '<button type="button" class="btn btn-danger btn-sm remove-meter">Remove</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
                $('#meters-container').append(meterHtml);
            }

            function addNoteRow() {
                var noteIndex = $('#notes-container .note-row').length;
                var noteHtml = '<div class="note-row border p-3 mb-3">' +
                    '<div class="row">' +
                        '<div class="col-md-3">' +
                            '<label class="form-label">Note Date</label>' +
                            '<input type="date" name="customer_notes[' + noteIndex + '][note_date]" class="form-control" required>' +
                        '</div>' +
                        '<div class="col-md-8">' +
                            '<label class="form-label">Note</label>' +
                            '<textarea name="customer_notes[' + noteIndex + '][note]" class="form-control" rows="2" required></textarea>' +
                        '</div>' +
                        '<div class="col-md-1">' +
                            '<label class="form-label">&nbsp;</label>' +
                            '<button type="button" class="btn btn-danger btn-sm remove-note">Remove</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
                $('#notes-container').append(noteHtml);
            }

            function clearDynamicContent() {
                $('#meters-container').empty();
                $('#notes-container').empty();
            }

            function loadBillingAreas(billingAreas) {
                if (billingAreas) {
                    billingAreas.forEach(function(area) {
                        $('input[name="billing_areas[' + area.area_type + '][hvac_area_sqft]"]').val(area.hvac_area_sqft);
                        $('input[name="billing_areas[' + area.area_type + '][hvac_rate_per_sqft]"]').val(area.hvac_rate_per_sqft);
                        $('input[name="billing_areas[' + area.area_type + '][maintenance_area_sqft]"]').val(area.maintenance_area_sqft);
                        $('input[name="billing_areas[' + area.area_type + '][maintenance_rate_per_sqft]"]').val(area.maintenance_rate_per_sqft);
                        $('input[name="billing_areas[' + area.area_type + '][rent_area_sqft]"]').val(area.rent_area_sqft);
                        $('input[name="billing_areas[' + area.area_type + '][rent_rate_per_sqft]"]').val(area.rent_rate_per_sqft);
                    });
                }
            }

            function loadElectricMeters(electricMeters) {
                $('#meters-container').empty();
                if (electricMeters) {
                    electricMeters.forEach(function(meter, index) {
                        var meterHtml = `
                            <div class="meter-row border p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Meter ID</label>
                                        <input type="text" name="electric_meters[${index}][meter_id]" class="form-control" value="${meter.meter_id}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Last Reading Electric</label>
                                        <input type="number" name="electric_meters[${index}][last_reading_electric]" class="form-control" value="${meter.last_reading_electric}" step="0.01">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Last Reading Genset</label>
                                        <input type="number" name="electric_meters[${index}][last_reading_genset]" class="form-control" value="${meter.last_reading_genset}" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Bill Type</label>
                                        <select name="electric_meters[${index}][bill_type]" class="form-select">
                                            <option value="postpaid" ${meter.bill_type === 'postpaid' ? 'selected' : ''}>Postpaid</option>
                                            <option value="prepaid" ${meter.bill_type === 'prepaid' ? 'selected' : ''}>Prepaid</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-meter">Remove</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#meters-container').append(meterHtml);
                    });
                }
            }

            function loadCustomerNotes(notes) {
                $('#notes-container').empty();
                if (notes) {
                    notes.forEach(function(note, index) {
                        var noteHtml = `
                            <div class="note-row border p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Note Date</label>
                                        <input type="date" name="customer_notes[${index}][note_date]" class="form-control" value="${note.note_date}" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Note</label>
                                        <textarea name="customer_notes[${index}][note]" class="form-control" rows="2" required>${note.note}</textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-note">Remove</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#notes-container').append(noteHtml);
                    });
                }
            }

            // Remove meter/note functionality
            $(document).on('click', '.remove-meter', function() {
                $(this).closest('.meter-row').remove();
                reindexMeters();
            });

            $(document).on('click', '.remove-note', function() {
                $(this).closest('.note-row').remove();
                reindexNotes();
            });

            function reindexMeters() {
                $('#meters-container .meter-row').each(function(index) {
                    $(this).find('input[name*="[meter_id]"]').attr('name', 'electric_meters[' + index + '][meter_id]');
                    $(this).find('input[name*="[last_reading_electric]"]').attr('name', 'electric_meters[' + index + '][last_reading_electric]');
                    $(this).find('input[name*="[last_reading_genset]"]').attr('name', 'electric_meters[' + index + '][last_reading_genset]');
                    $(this).find('select[name*="[bill_type]"]').attr('name', 'electric_meters[' + index + '][bill_type]');
                });
            }

            function reindexNotes() {
                $('#notes-container .note-row').each(function(index) {
                    $(this).find('input[name*="[note_date]"]').attr('name', 'customer_notes[' + index + '][note_date]');
                    $(this).find('textarea[name*="[note]"]').attr('name', 'customer_notes[' + index + '][note]');
                });
            }
        });
    </script>
@endpush 