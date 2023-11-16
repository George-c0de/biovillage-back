<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\OrderGiftModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Searchers\OrderGiftsSearcher;
use App\Searchers\OrderItemsSearcher;
use App\Searchers\OrderSearcher;
use App\Searchers\ProductSearcher;
use App\Service\Images\ImageService;
use Faker\Provider\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Order Item Service.
 *
 * @package App\Service\Section
 */
class OrderItemService
{
    /**
     * Search item by id
     * @param $itemId
     * @return null
     */
    public static function searchItemById($itemId) {
        $s = new OrderItemsSearcher();
        $items = $s->search(['id' => $itemId]);
        if(!empty($items)) {
            return $items[0];
        }
        return null;
    }

    /**
     * Search many items
     * @param $ids
     * @return null
     */
    public static function searchItemsByIds($ids) {
        $s = new OrderItemsSearcher();
        return $s->search(['ids' => $ids]);
    }

    /**
     * Update order item data
     * @param $params
     * @return OrderItemModel
     */
    public static function updateItem($params) {
        $i = OrderItemModel::findOrFail($params['id'] ?? -1);
        $i->fill($params);
        $i->save();
        return $i;
    }

    /**
     * Search item by id
     * @param $itemId
     * @return null
     */
    public static function searchGiftById($itemId) {
        $s = new OrderGiftsSearcher();
        $items = $s->search(['id' => $itemId]);
        if(!empty($items)) {
            return $items[0];
        }
        return null;
    }

    /**
     * Search many items
     * @param $ids
     * @return null
     */
    public static function searchOrderGiftsByIds($ids) {
        $s = new OrderGiftsSearcher();
        return $s->search(['ids' => $ids]);
    }

    /**
     * Update order gift data
     * @param $params
     * @return OrderGiftModel
     */
    public static function updateGift($params) {
        $i = OrderGiftModel::findOrFail($params['id'] ?? -1);
        $i->fill($params);
        $i->save();
        return $i;
    }

    /**
     * Mass update order items
     * @param $items
     * @param $gifts
     */
    public static function massUpdate($items, $gifts) {

        // Update items
        if(empty($items)) {
            $items = [];
        }
        $table = with(new OrderItemModel())->getTable();
        foreach($items as $item) {
            $itemId = $item['id'];
            if(empty($itemId)) {
                continue;
            }
            unset($item['id']);
            DB::table($table)->where('id', '=', $itemId )
                ->update($item);
        }

        // Update gifts
        if(empty($gifts)) {
            $gifts = [];
        }
        $table = with(new OrderGiftModel())->getTable();
        foreach($gifts as $gift) {
            $giftId = $gift['id'];
            if(empty($giftId)) {
                continue;
            }
            unset($gift['id']);
            DB::table($table)->where('id', '=', $giftId )
                ->update($gift);
        }
    }

    /**
     * Cancel order item
     * @param $itemId
     */
    public static function cancelOrderItem($itemId)
    {
        DB::statement('
            UPDATE "orderItems" SET "deletedAt" = CURRENT_TIMESTAMP
            WHERE id = ?
        ', [$itemId]);
    }

    /**
     * Cancel order item
     * @param $giftItemId
     */
    public static function cancelOrderGift($giftItemId)
    {
        DB::statement('
            UPDATE "orderGifts" SET "deletedAt" = CURRENT_TIMESTAMP
            WHERE id = ?
        ', [$giftItemId]);
    }

}
