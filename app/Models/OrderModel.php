<?php namespace App\Models;

use App\Models\Auth\Client;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order model.
 *
 * @package App\Models
 */
class OrderModel extends BaseModel
{
    const DEFAULT_PER_PAGE = 25;

    const SORT_CREATED = 'createdAt';
    const SORT_UPDATED = 'updatedAt';
    const SORT_FINISHED = 'finishedAt';
    const SORT_PACKED = 'packedAt';
    const SORT_STATUS = 'status';
    const SORT_INTERVAL = 'interval';
    const SORT_CLIENT = 'clientName';
    const SORT_DELIVERY = 'deliveryDate';
    const SORTS = [
        self::SORT_CREATED, self::SORT_UPDATED, self::SORT_FINISHED,
        self::SORT_INTERVAL, self::SORT_STATUS, self::SORT_CLIENT,
        self::SORT_PACKED, self::SORT_DELIVERY,

        // old
        'created', 'updated', 'finished'
    ];

    const STATUS_NEW = 'new';           // New order. Payment is processing
    const STATUS_CANCEL = 'canceled';   // Order is canceled
    const STATUS_PLACED = "placed";     // Payment is received
    const STATUS_DELIVERY = "delivery"; // In delivery
    const STATUS_FINISHED = "finished"; // Order is finished. Client is satisfied
    const STATUS_PACKED = 'packed';     // Order packed
    const STATUES = [ self::STATUS_NEW, self::STATUS_CANCEL,
        self::STATUS_PLACED, self::STATUS_DELIVERY, self::STATUS_FINISHED,
        self::STATUS_PACKED
    ];
    const ENDED_STATUSES = [
        self::STATUS_CANCEL, self::STATUS_FINISHED
    ];

    // Date field for certain status
    const STATUS_DATES_MAP = [
        self::STATUS_FINISHED => 'finishedAt',
        self::STATUS_PLACED => 'placedAt',
        self::STATUS_PACKED => 'packedAt',
        self::STATUS_CANCEL => 'finishedAt',
    ];


    // What do if not product
    const ACTION_FIND_ANALOG = 'findAnalog';
    const ACTION_NOT_CALL_AND_BUY = 'notCallNotBuy';
    const ACTIONS_IF_NOT_DELIVERY = [
      self::ACTION_FIND_ANALOG,
      self::ACTION_NOT_CALL_AND_BUY
    ];

    public $timestamps = false;

    protected $table = 'orders';

    protected $fillable = [
        'status',
        'adminsComment',
        'commentForClient',
        'deliveryDate',
        'deliveryIntervalId',
        'actionIfNotDelivery',
    ];

    /**
     * Field that cache calculated products sum
     * @var int $cachedProductsSum
     */
    public $cachedProductsSum = 0;

    /**
     * Get order model by id
     * @param $order
     * @return
     */
    public static function orderInstance($order)
    {
        if (!$order instanceof OrderModel) {
            $order = OrderModel::findOrFail($order);
        }
        return $order;
    }

    /**
     * Primary order payment instance
     * @return mixed
     */
    public function primaryPayment() {
        return PaymentModel::where('orderId', $this->id)
            ->orderBy('createdAt', 'ASC')
            ->firstOrFail();
    }

    /**
     * Client instance
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId')->first();
    }

    /**
     * Order items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function items() {
        return $this->hasMany(OrderItemModel::class, 'orderId')->get();
    }


}
