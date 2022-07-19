<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    use SoftDeletes;

    protected $fillable = [
        'name',
        'pre_qty',
        'pre_item_id',
        'pre_type',
        'benefit_qty',
        'benefit_item_id',
        'benefit_discount_amount',
        'benefit_discount_type',
        'benefit_type',
        'apply_multiply'
    ];

    /**
     * Pre Type: Item or Ticket | Pre Type: Member
     * -------------------------|---------------------
     * Buy {Pre Qty} {Pre Item} | Has {Pre Qty} member
     * 
     * 
     * Benefit Type: Item		 | Benefit Type: Item and Discount			       | Benefit Type: Discount
     * ---------------------------------|-------------------------------------------------------------|---------------------------
     * get {Benefit Qty} {Benefit Item} | get {Benefit Qty} {Benefit Item} and {Benefit Discount} off | get {Benefit Discount} off
     */
    public function getDescriptionAttribute()
    {
        $description = "";

        // description for pre type
        switch (strtolower($this->pre_type)) {
            case 'item':
            case 'ticket':
                $pre_item_name = Item::withTrashed()->where('id', $this->pre_item_id)->first()->name;
                $description = "Buy {$this->pre_qty} {$pre_item_name}(s) ";
                break;
            case 'member':
                $description = "Has {$this->pre_qty} member(s) ";
                break;
            default:
                break;
        }

        // description for benefit type
        switch (strtolower($this->benefit_type)) {
            case 'free item':
                $benefit_name = Item::withTrashed()->where('id', $this->benefit_item_id)->first()->name;
                $description = $description . "get {$this->benefit_qty} {$benefit_name}(s).";
                break;
            case 'item and discount':
                $benefit_name = Item::withTrashed()->where('id', $this->benefit_item_id)->first()->name;
                // discount info example value either 10% or 100.000 IDR
                $number = number_format($this->benefit_discount_amount);
                $discount_info = strtolower($this->benefit_discount_type) == "percentage" ? "{$this->benefit_discount_amount}%" : "{$number} IDR";
                $description = $description . "get {$this->benefit_qty} {$benefit_name}(s) and {$discount_info} off.";
                break;
            case 'discount':
                // discount info example value either 10% or 100.000 IDR
                $number = number_format($this->benefit_discount_amount);
                $discount_info = strtolower($this->benefit_discount_type) == "percentage" ? "{$this->benefit_discount_amount}%" : "{$number} IDR";
                $description = $description . "get {$discount_info} off.";
                break;
            default:
                break;
        }

        return $description;
    }

    public function getPriceAttribute()
    {
        $result = 0;

        switch (strtolower($this->pre_type)) {
            case 'item':
            case 'ticket':
                $pre_single_price = Item::withTrashed()->where('id', $this->pre_item_id)->first()->price;
                $result += $this->pre_qty * $pre_single_price;
                break;
            default:
                break;
        }

        // description for benefit type
        switch (strtolower($this->benefit_type)) {
            case 'free item':
                // free item no price calculation needed
                break;
            case 'item and discount':
                $benefit_single_price = Item::withTrashed()->where('id', $this->benefit_item_id)->first()->price;
                $reduction = strtolower($this->benefit_discount_type) == "percentage" 
                    ? $this->benefit_qty * ($this->benefit_discount_amount / 100) * $benefit_single_price 
                    : $this->benefit_discount_amount;
                $result += $benefit_single_price * $this->benefit_qty - $reduction;
                break;
            case 'discount':
                $reduction = strtolower($this->benefit_discount_type) == "percentage" ? $result * ($this->benefit_discount_amount / 100) : $this->benefit_discount_amount;
                $result -= $reduction;
                break;
            default:
                break;
        }

        return $result;
    }

    public function salesItems()
    {
        return $this->morphMany('App\Models\SalesItem', 'sellable');
    }

    public function preItem()
    {
        return $this->belongsTo('App\Models\Item', 'pre_item_id', 'id');
    }

    public function benefitItem()
    {
        return $this->belongsTo('App\Models\Item', 'benefit_item_id', 'id');
    }
}
