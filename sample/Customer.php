<?php

namespace App\Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'ntn',
        'strn',
        'address',
        'email',
        'phone',
        'sqft',
        'credit_terms',
        'cell',
        'credit_limit',
        'chart_of_account_id',
        'status',
        'customer_category_id',
        'customer_floor_id',
        'notes',
        'opening_date',
        'unit_value_for_rent',
        'unit_value_for_rent_percent',
        'minimum_rent',
        'hvac_billing_by',
        'hvac_tons',
        'hvac_hour_rate',
        'operation_hours',
        'shop_nos',
        'gas_meter_id',
        'gas_rate',
        'gas_last_reading',
        'post_paid_electric',
        'post_paid_genset',
        'hvac',
        'maintenance',
        'gas',
        'rent',
        'electric_unit_rate',
        'genset_unit_rate',
        'tax_wht_percent',
        'tax_srb_percent',
    ];

    protected $casts = [
        'sqft' => 'decimal:2',
        'credit_terms' => 'integer',
        'credit_limit' => 'decimal:2',
        'unit_value_for_rent' => 'decimal:2',
        'unit_value_for_rent_percent' => 'decimal:2',
        'minimum_rent' => 'decimal:2',
        'hvac_tons' => 'decimal:2',
        'hvac_hour_rate' => 'decimal:2',
        'operation_hours' => 'decimal:2',
        'gas_rate' => 'decimal:2',
        'gas_last_reading' => 'decimal:2',
        'electric_unit_rate' => 'decimal:2',
        'genset_unit_rate' => 'decimal:2',
        'tax_wht_percent' => 'decimal:2',
        'tax_srb_percent' => 'decimal:2',
        'opening_date' => 'date',
        'post_paid_electric' => 'boolean',
        'post_paid_genset' => 'boolean',
       
        'hvac' => 'boolean',
        'maintenance' => 'boolean',
        'gas' => 'boolean',
        'rent' => 'boolean'
    ];

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function customerCategory(): BelongsTo
    {
        return $this->belongsTo(CustomerCategory::class);
    }

    public function customerFloor(): BelongsTo
    {
        return $this->belongsTo(CustomerFloor::class);
    }

    public function billingAreas(): HasMany
    {
        return $this->hasMany(CustomerBillingArea::class);
    }

    public function electricMeters(): HasMany
    {
        return $this->hasMany(CustomerElectricMeter::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
} 