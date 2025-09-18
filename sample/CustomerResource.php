<?php

namespace App\Modules\Accounts\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'encrypted_id' => _encrypt_id($this->id),
            'name' => $this->name,
            'ntn' => $this->ntn,
            'strn' => $this->strn,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'sqft' => $this->sqft,
            'credit_terms' => $this->credit_terms,
            'cell' => $this->cell,
            'credit_limit' => $this->credit_limit,
            'chart_of_account_id' => $this->chart_of_account_id,
            'chart_of_account_name' => $this->chartOfAccount?->name,
            'chart_of_account_code' => $this->chartOfAccount?->code,
            'parent_account_id' => $this->chartOfAccount?->parent_id,
            'parent_account_name' => $this->chartOfAccount?->parent?->name,
            'parent_account_code' => $this->chartOfAccount?->parent?->code,
            'status' => $this->status,
            
            // Contact Information
            'customer_category_id' => $this->customer_category_id,
            'customer_category_name' => $this->customerCategory?->name,
            'customer_floor_id' => $this->customer_floor_id,
            'customer_floor_name' => $this->customerFloor?->name,
            'notes' => $this->notes,
            
            // Billing Information
            'opening_date' => $this->opening_date?->format('Y-m-d'),
            'unit_value_for_rent' => $this->unit_value_for_rent,
            'unit_value_for_rent_percent' => $this->unit_value_for_rent_percent,
            'minimum_rent' => $this->minimum_rent,
            'hvac_billing_by' => $this->hvac_billing_by,
            'hvac_tons' => $this->hvac_tons,
            'hvac_hour_rate' => $this->hvac_hour_rate,
            'operation_hours' => $this->operation_hours,
            'shop_nos' => $this->shop_nos,
            'gas_meter_id' => $this->gas_meter_id,
            'gas_rate' => $this->gas_rate,
            'gas_last_reading' => $this->gas_last_reading,
            'post_paid_electric' => $this->post_paid_electric,
            'post_paid_genset' => $this->post_paid_genset,
            'hvac' => $this->hvac,
            'maintenance' => $this->maintenance,
            'gas' => $this->gas,
            'rent' => $this->rent,
            'tax_wht_percent' => $this->tax_wht_percent,
            'tax_srb_percent' => $this->tax_srb_percent,
            // Electric Bill Setup
            'electric_unit_rate' => $this->electric_unit_rate,
            'genset_unit_rate' => $this->genset_unit_rate,
            
            // Relationships
            'billing_areas' => $this->billingAreas,
            'electric_meters' => $this->electricMeters,
            'customer_notes' => $this->notes,
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 