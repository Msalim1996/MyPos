<?php

namespace App\Http\Controllers\Api;

use App\Enums\ItemType;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemStoreRequest as StoreRequest;
use App\Http\Requests\ItemUpdateRequest as UpdateRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

/**
 * @group Item CRUD
 */
class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET all
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = QueryBuilder::for(Item::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['stocks', 'stocks.location', 'member_discount'])
            ->get();
        return ItemResource::collection($items);
    }

    /**
     * POST (New Item)
     * 
     * Item receive several data such as item and stocks information
     * 
     * bodyParam:
     * {
     *   name: "value",
     *   sku: "value",
     *   price: 0,
     *   type: "value",
     *   category: "value",
     *   description: "value",
     *   uom: "value",
     *   image: "value:,
     *   stocks: [
     *     {
     *       location_id: 1,
     *       quantity: 100,
     *     }
     *   ]
     * }
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $item = Item::create([
            'name' => $request->input('name'),
            'sku' => $request->input('sku'),
            'price' => $request->input('price'),
            'purchase_price' => $request->input('purchase_price'),
            'type' => $request->input('type'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'uom' => $request->input('uom'),
            'tax' => $request->input('tax'),
        ]);

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $item->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(Item::$mediaCollectionPath);
        }

        if ($request->stocks) {
            for ($index = 0; $index < count($request->stocks); $index++) {
                Stock::create([
                    'qty' => $request->input('stocks.' . $index . '.qty'),
                    'item_id' => $item->id,
                    'location_id' => $request->input('stocks.' . $index . '.location_id'),
                ]);
            }
        }

        return new ItemResource($item);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = Item::withTrashed()->where('id', $id);
        $item = QueryBuilder::for($query)
            ->allowedIncludes(['stocks', 'stocks.location', 'member_discount'])
            ->firstOrFail();
        return new ItemResource($item);
    }

    /**
     * PUT
     * 
     * update item and stocks information
     * 
     * bodyParam:
     * {
     *   name: "value",
     *   sku: "value",
     *   price: 0,
     *   type: "value",
     *   category: "value",
     *   description: "value",
     *   uom: "value",
     *   image: "value:,
     *   stocks: [
     *     {
     *       location_id: 1,
     *       quantity: 100,
     *     }
     *   ]
     * } 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Item $item)
    {
        $item->update([
            'name' => $request->input('name'),
            'sku' => $request->input('sku'),
            'price' => $request->input('price'),
            'purchase_price' => $request->input('purchase_price'),
            'type' => $request->input('type'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'uom' => $request->input('uom'),
            'tax' => $request->input('tax'),
        ]);
        $item->clearMediaCollection(Item::$mediaCollectionPath);

        // if image is provided, also upload image
        if ($request->image) {

            if ($request->input('image') != "") {
                $item->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                    ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                    ->toMediaCollection(Item::$mediaCollectionPath);
            }
        }
        
        $stockIds = [];
        if ($request->stocks) {
            for ($index = 0; $index < count($request->stocks); $index++) {
                $stock = Stock::updateOrCreate([
                    'location_id' => $request->input('stocks.' . $index . '.location_id'),
                    'item_id' => $item->id
                ], [
                    'qty' => $request->input('stocks.' . $index . '.qty'),
                ]);

                array_push($stockIds, $stock->id);
            }
        }
        $item->stocks()->whereNotIn('id', $stockIds)->delete();

        return new ItemResource($item);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        if (count($item->shortcutProducts) != 0) 
            return response()->json(['message' => 'Item ini masih terhubung dengan shortcut. Hapus shortcut yang berhubungan terlebih dahulu'], 400);
        
            $item->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, int $id)
    {
        $item = Item::withTrashed()->findOrFail($id);
        if ($item->trashed()) $item->restore();

        return new ItemResource($item);
    }

    /**
     * GET item types
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getItemTypes(Request $request)
    {
        return response()->json(['data' => ItemType::getValues()], 200);
    }

    /**
     * get all item and location crossed join to retrieve stocks.
     * 
     * result: 
     * [
     *     {
     *         "id": 1,
     *         "name": "kok",
     *         "stocks": [
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             },
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             },
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             }
     *         ]
     *     },
     *     {
     *         "id": 2,
     *         "name": "khk",
     *         "stocks": [
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             },
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             },
     *             {
     *                 "id": 0,
     *                 "qty": 0
     *             }
     *         ]
     *     }
     * ]
     */
    public function getItemStocks()
    {
        $records = DB::table('locations')
            ->crossJoin('items')
            ->leftJoin('stocks', function ($join) {
                $join->on('stocks.location_id', '=', 'locations.id');
                $join->on('stocks.item_id', '=', 'items.id');
            })
            ->select('items.id as item_id', 'items.name as item_name', 'items.sku as item_sku', 'items.description as item_description',
                    'items.category as item_category', 'stocks.id as stock_id', 'stocks.qty as stock_qty', 'locations.id as location_id', 'locations.name as location_name')
            ->where('items.deleted_at','=',null)
            ->where('items.type', '=', 'Stock')
            ->orWhere('items.type', '=', 'Ticket')
            ->get();

        $result = array();
        foreach ($records as $record) {
            $stock = array(
                "id" => isset($record->stock_id) ? $record->stock_id : 0,
                "qty" => isset($record->stock_qty) ? $record->stock_qty : 0,
                "location" => array(
                    "id" => isset($record->location_id) ? $record->location_id : 0,
                    "name" => isset($record->location_name) ? $record->location_name : 0,
                )
            );

            // append stock into stock list and location list if item found
            if (array_key_exists($record->item_name, $result)) {
                array_push($result[$record->item_name]["stocks"], $stock);
            } else {

                // if item is not found, create a new item and initialize stock list
                $item = array(
                    "id" => $record->item_id,
                    "name" => $record->item_name,
                    "sku" => $record->item_sku,
                    "description" => $record->item_description,
                    "category" => $record->item_category,
                    "stocks" => array($stock)
                );
                $result[$record->item_name] = $item;
            }
        }
        return response()->json(['data' => array_values($result)], 200);
    }
}
