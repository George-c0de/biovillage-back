<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Payment model.
 *
 * @package App\Models
 */
class PaymentModel extends BaseModel
{

    // Payment statuses
    const PAYMENT_WAIT = 'waiting';
    const PAYMENT_CANCEL = 'cancel';
    const PAYMENT_DONE = 'done';
    const PAYMENT_STATUSES = [
        self::PAYMENT_WAIT, self::PAYMENT_DONE, self::PAYMENT_CANCEL
    ];

    // Transaction type
    const TRANSACTION_PAY = 'pay';
    const TRANSACTION_REFUND = 'refund';
    const TRANSACTION_TYPES = [
        self::TRANSACTION_PAY, self::TRANSACTION_REFUND
    ];

    // Payment types
    const METHOD_CASH = 'cash';
    const METHOD_COURIER_CARD = 'ccard';
    const METHOD_GOOGLE_PAY = 'gpay';
    const METHOD_APPLE_PAY = 'apay';
    const METHOD_BANK_CARD = 'card';
    const METHOD_BONUS = 'bonus';
    const PAYMENT_METHODS = [
        self::METHOD_APPLE_PAY,
        self::METHOD_GOOGLE_PAY,
        self::METHOD_CASH,
        self::METHOD_COURIER_CARD,
        self::METHOD_BANK_CARD,
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $timestamps = false;

    protected $table = 'payments';

    protected $fillable = [
    ];


    /**
     * OrderInstance
     */
    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'orderId');
    }

}
