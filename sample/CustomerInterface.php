<?php

namespace App\Modules\Accounts\Repositories\Interfaces;

use App\Repositories\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Model;

interface CustomerInterface extends BaseInterface
{
    public function getModel(): Model;
    public function getFilteredCustomers(array $filters);
    public function getByChartOfAccount($chartOfAccountId);
    public function updateOrCreate(array $attributes, array $values = []): Model;
} 