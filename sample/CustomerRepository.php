<?php

namespace App\Modules\Accounts\Repositories\Eloquent;

use App\Modules\Accounts\Models\Customer;
use App\Modules\Accounts\Repositories\Interfaces\CustomerInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class CustomerRepository extends BaseRepository implements CustomerInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function getFilteredCustomers(array $filters)
    {
        $query = $this->model->with([
            'chartOfAccount.parent',
            'customerCategory',
            'customerFloor',
            'billingAreas',
            'electricMeters',
            'notes'
        ]);

        if (!empty($filters['filter_search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['filter_search'] . '%')
                  ->orWhere('ntn', 'like', '%' . $filters['filter_search'] . '%')
                  ->orWhere('strn', 'like', '%' . $filters['filter_search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['filter_search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['filter_search'] . '%');
            });
        }

        if (!empty($filters['filter_status'])) {
            $query->where('status', $filters['filter_status']);
        }

        if (!empty($filters['filter_head'])) {
            $query->whereHas('chartOfAccount', function($q) use ($filters) {
                $q->where('parent_id', $filters['filter_head']);
            });
        }

        if (!empty($filters['filter_category'])) {
            $query->where('customer_category_id', $filters['filter_category']);
        }

        if (!empty($filters['filter_floor'])) {
            $query->where('customer_floor_id', $filters['filter_floor']);
        }

        return $query->get();
    }

    public function getByChartOfAccount($chartOfAccountId)
    {
        return $this->model->where('chart_of_account_id', $chartOfAccountId)->first();
    }
} 