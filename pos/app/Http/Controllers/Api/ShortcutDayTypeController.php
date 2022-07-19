<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\ShortcutDayTypeStoreRequest as StoreRequest;
use App\Http\Requests\ShortcutDayTypeUpdateRequest as UpdateRequest;
use App\Http\Resources\ShortcutDayTypeResource;
use App\Models\ShortcutDay;
use App\Models\ShortcutDayType;
use App\Models\ShortcutProduct;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Product Shortcut Functionality
 *
 * @group Shortcut CRUD & functionality
 */
class ShortcutDayTypeController extends Controller
{
    /**
     * Shortcut Day Type GET all
     *
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shortcutDayTypes = QueryBuilder::for(ShortcutDayType::class)
            ->allowedIncludes(['shortcut_days', 'shortcut_products', 'shortcut_products.item'])
            ->get();
        return ShortcutDayTypeResource::collection($shortcutDayTypes);
    }

    /**
     * Shortcut Day Type POST
     *
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $shortcutDayType = ShortcutDayType::create([
            'name' => $request->name,
        ]);

        if ($request->shortcut_days) {
            for ($index = 0; $index < count($request->shortcut_days); $index++) {
                ShortcutDay::create([
                    'on_date' => $request->input('shortcut_days.' . $index . '.on_date'),
                    'description' => $request->input('shortcut_days.' . $index . '.description'),
                    'shortcut_day_type_id' => $shortcutDayType->id,
                ]);
            }
        }

        if ($request->shortcut_products) {
            for ($index = 0; $index < count($request->shortcut_products); $index++) {
                ShortcutProduct::create([
                    'shortcut_key' => $request->input('shortcut_products.' . $index . '.shortcut_key'),
                    'category' => $request->input('shortcut_products.' . $index . '.category'),
                    'position_index' => $request->input('shortcut_products.' . $index . '.position_index'),
                    'item_id' => $request->input('shortcut_products.' . $index . '.item_id'),
                    'shortcut_day_type_id' => $shortcutDayType->id,
                ]);
            }
        }

        return new ShortcutDayTypeResource($shortcutDayType->load(['shortcutDays', 'shortcutProducts', 'shortcutProducts.items']));
    }

    /**
     * Shortcut Day Type GET
     *
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = ShortcutDayType::where('id', $id);
        $shortcutDayTypes = QueryBuilder::for($query)
            ->allowedIncludes(['shortcut_days', 'shortcut_products', 'shortcut_products.item'])
            ->firstOrFail();
        return new ShortcutDayTypeResource($shortcutDayTypes);
    }

    /**
     * Shortcut Day Type PUT
     *
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, ShortcutDayType $shortcutDayType)
    {
        $shortcutDayType->update([
            'name' => $request->name,
        ]);

        $shortcutDays = [];
        if ($request->shortcut_days) {
            for ($index = 0; $index < count($request->shortcut_days); $index++) {
                $shortcutDay = ShortcutDay::updateOrCreate([
                    'id' => $request->input('shortcut_days.' . $index . '.id'),
                ], [
                    'on_date' => $request->input('shortcut_days.' . $index . '.on_date'),
                    'description' => $request->input('shortcut_days.' . $index . '.description'),
                    'shortcut_day_type_id' => $shortcutDayType->id,
                ]);

                array_push($shortcutDays, $shortcutDay->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempShortcutDayTypes = $shortcutDayType->shortcutDays()->whereNotIn('id', $shortcutDays)->get();
        foreach ($tempShortcutDayTypes as $tempShortcutDayType) $tempShortcutDayType->delete();

        $shortcutProducts = [];
        if ($request->shortcut_products) {
            for ($index = 0; $index < count($request->shortcut_products); $index++) {
                $shortcutProduct = ShortcutProduct::updateOrCreate([
                    'id' => $request->input('shortcut_products.' . $index . '.id'),
                ], [
                    'category' => $request->input('shortcut_products.' . $index . '.category'),
                    'shortcut_key' => $request->input('shortcut_products.' . $index . '.shortcut_key'),
                    'position_index' => $request->input('shortcut_products.' . $index . '.position_index'),
                    'item_id' => $request->input('shortcut_products.' . $index . '.item_id'),
                    'shortcut_day_type_id' => $shortcutDayType->id,
                ]);

                array_push($shortcutProducts, $shortcutProduct->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempShortcutProducts = $shortcutDayType->shortcutProducts()->whereNotIn('id', $shortcutProducts)->get();
        foreach ($tempShortcutProducts as $tempShortcutProduct) $tempShortcutProduct->delete();

        return new ShortcutDayTypeResource($shortcutDayType->load(['shortcutDays', 'shortcutProducts', 'shortcutProducts.item']));
    }

    /**
     * Shortcut Day Type DELETE
     *
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shortcutDayTypeId)
    {
        $shortcutDayType = ShortcutDayType::where('id','=',$shortcutDayTypeId);

        $shortcutProducts = ShortcutProduct::where('shortcut_day_type_id','=',$shortcutDayTypeId)->get();
        foreach($shortcutProducts as $shortcutProduct) $shortcutProduct->delete();
        
        $shortcutDays = ShortcutDay::where('shortcut_day_type_id','=',$shortcutDayTypeId)->get();
        foreach($shortcutDays as $shortcutDay) $shortcutDay->delete();

        $shortcutDayType->delete();

        return response()->json(null, 204);
    }
}
