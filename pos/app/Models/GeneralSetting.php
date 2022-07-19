<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    public static $mediaCollectionPath = "logo-images";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gate_control_type',
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'skating_aid_timeout',
        'tax_payer',
        'tax_number',
        'affirmation_date',
        'logo',
        'tax_toggle',
        'tax_amount',
        'gate_mode'
    ];
}
