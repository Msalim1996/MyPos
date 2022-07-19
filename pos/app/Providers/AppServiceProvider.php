<?php

namespace App\Providers;

use App\Models\AdjustItem;
use App\Models\PurchaseFulfillment;
use App\Models\PurchaseOrder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\SalesFulfillment;
use App\Models\SalesItem;
use App\Models\SalesOrder;
use App\Models\SalesPayment;
use App\Models\SalesRestock;
use App\Models\SalesReturn;
use App\Models\AdjustOrder;
use App\Models\StudentClass;
use App\Models\TransferItem;
use App\Models\TransferOrder;
use App\Observers\AdjustItemObserver;
use App\Observers\PurchaseFulfillmentObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\SalesFulfillmentStockObserver;
use App\Observers\SalesItemStockObserver;
use App\Observers\SalesOrderObserver;
use App\Observers\SalesPaymentObserver;
use App\Observers\SalesRestockStockObserver;
use App\Observers\SalesReturnStockObserver;
use App\Observers\AdjustOrderObserver;
use App\Observers\StudentClassObserver;
use App\Observers\TransferItemObserver;
use App\Observers\TransferOrderObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        SalesItem::observe(SalesItemStockObserver::class);
        SalesFulfillment::observe(SalesFulfillmentStockObserver::class);
        SalesReturn::observe(SalesReturnStockObserver::class);
        SalesRestock::observe(SalesRestockStockObserver::class);
        SalesOrder::observe(SalesOrderObserver::class);
        SalesPayment::observe(SalesPaymentObserver::class);
        StudentClass::observe(StudentClassObserver::class);
        PurchaseFulfillment::observe(PurchaseFulfillmentObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        AdjustOrder::observe(AdjustOrderObserver::class);
        AdjustItem::observe(AdjustItemObserver::class);
        TransferOrder::observe(TransferOrderObserver::class);
        TransferItem::observe(TransferItemObserver::class);

        Relation::morphMap([
            'Sales fulfillment' => 'App\Models\SalesFulfillment',
            'Sales restock' => 'App\Models\SalesRestock',
            'Sales return' => 'App\Models\SalesReturn',
            'Purchase fulfillment' => 'App\Models\PurchaseFulfillment',
            'Transfer item' => 'App\Models\TransferItem',
            'Adjust item' => 'App\Models\AdjustItem',
        ]);
        
        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            if ($value)
            {
                try {
                    $image = Image::make($value);
                    $size = strlen(base64_decode($value));
                    $size_kb = $size / 1024;
                    return $size_kb <= $parameters[0];
                } catch (\Exception $e) {
                    return false;
                }
            }
            return true;
        });

        Validator::replacer('base64image', function($message, $attribute, $rule, $parameters) {
            $base64image = $parameters[0];

            return str_replace(':base64image', $base64image, 'Size Item terlalu besar!');
        });
    }
}
