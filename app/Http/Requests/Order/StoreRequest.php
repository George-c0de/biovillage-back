<?php

namespace App\Http\Requests\Order;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\SettingsModel;
use App\Service\AddressService;
use App\Service\PaymentService;
use App\Service\ProductService;
use App\Service\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\RequiredIf;

class StoreRequest extends BaseApiRequest {

    public function rules() {
        return [
            'deliveryIntervalId' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:deliveryIntervals,id,deletedAt,NULL',
            'addressId' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:addresses,id,clientId,' . Auth::id() . ',deletedAt,NULL',
            'clientsComment' => 'nullable|string|max:5000',
            'actionIfNotDelivery' => [
                'nullable',
                Rule::in(OrderModel::ACTIONS_IF_NOT_DELIVERY)
            ],
            'promoCode' => 'nullable|string|min:2|max:64',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:products,id,deletedAt,NULL',
            'products.*.qty' => 'required|integer|min:1|max:' . DbHelper::MAX_INT,
            'products.*.price' => 'required|integer|min:1|max:' . DbHelper::MAX_INT,
            'products.*.total' => 'required|integer|min:1|max:' . DbHelper::MAX_INT,
            'gifts' => 'nullable|array',
            'gifts.*.id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:gifts,id,deletedAt,NULL',
            'gifts.*.qty' => 'integer|min:1|max:' . DbHelper::MAX_INT,
            'gifts.*.price' => 'integer|min:1|max:' . DbHelper::MAX_INT,
            'gifts.*.total' => 'integer|min:1|max:' . DbHelper::MAX_INT,
            'paymentPrimaryMethod' => [
                'required',
                Rule::in(PaymentModel::PAYMENT_METHODS)
            ],
            'paymentToken' => [
                new RequiredIf(PaymentService::throughGateway($this->paymentPrimaryMethod)),
                'string',
                'max:254'
            ],
//            'paymentPrimaryData' => [
//                'required_if:paymentPrimaryType,' . implode(',', [
//                    PaymentModel::PAYMENT_APPLE_PAY, PaymentModel::PAYMENT_GOOGLE_PAY
//                ]),
//                'string',
//                'max:10000'
//            ],
            'productsSum' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
            'deliverySum' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
            'giftBonuses' => 'integer|min:0|max:'.DbHelper::MAX_INT,
            'total' => 'required|integer|min:1|max:'.DbHelper::MAX_INT
        ];
    }

    /**
     * Validate delivery and delivery sum
     */
    private function validateDelivery() {
        // Check delivery sum
        $expectedDeliverySum = AddressService::calcDelivery(
            $this->addressId, $this->productsSum
        );
        // Strange error, delivery price is empty
        if (is_null($expectedDeliverySum)) {
            return false;
        }
        return intval($expectedDeliverySum) === intval($this->deliverySum);
    }

    /**
     * Validate request products, sum and total for each product and together
     */
    private function validateProducts() {

        // Ignoring bellow
        // The customer want
        return true;

        // Check products total
        $prodIds = array_column($this->products, 'id');
        $qty = array_column($this->products, 'qty');
        if(count($prodIds) != count($qty)) {
            return false;
        }
        $prodQty = array_combine($prodIds, $qty);
        $expectedSum = ProductService::calcProductsSum($prodQty);

        // Debugging
        Log::debug('New order', [
            'prods' => $prodQty,
            'es' => $expectedSum,
            'ps' => $this->productsSum
        ]);

        if(empty($expectedSum) || $expectedSum != intval($this->productsSum)) {
            return false;
        }

        // Check products total by request products
        // Two ways by total and by price * gty
        $totalByRequestProducts = array_sum(array_map(function($prod) {
            return $prod['total'];
        }, $this->products));
        if($expectedSum != $totalByRequestProducts) {
            return false;
        }
        $totalByRequestProducts = array_sum(array_map(function($prod) {
            return $prod['price'] * $prod['qty'];
        }, $this->products));
        if($expectedSum != $totalByRequestProducts) {
            return false;
        }

        return true;
    }

    /**
     * Validate request gifts and enough bonuses
     */
    private function validateGifts() {

        // No gifts
        if(empty($this->gifts)) {
            if(intval($this->giftBonuses ?? 0) > 0){
                return false;
            }
            return true;
        }

        $giftIds = array_column($this->gifts, 'id');
        $qty = array_column($this->gifts, 'qty');
        if(count($giftIds) != count($qty)) {
            return false;
        }
        $giftQty = array_combine($giftIds, $qty);
        $expectedBonuses = app()->make('Gifts')->calcGiftsBonuses($giftQty);
        if(empty($expectedBonuses) || $expectedBonuses != intval($this->giftBonuses)) {
            return false;
        }

        // Check gift bonuses by request bonuses
        // Two ways by total and by price * gty
        $totalByRequestGifts = array_sum(array_map(function($gift) {
            return $gift['total'];
        }, $this->gifts));
        if($expectedBonuses != $totalByRequestGifts) {
            return false;
        }
        $totalByRequestGifts = array_sum(array_map(function($gift) {
            return $gift['price'] * $gift['qty'];
        }, $this->gifts));
        if($expectedBonuses != $totalByRequestGifts) {
            return false;
        }

        return true;
    }

    /**
     * Additions check
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Continue if no errors
            if($validator->errors()->count() > 0) {
                return;
            }

//            if(!$this->validateDelivery()) {
//                $validator->errors()->add('addressId', trans('errors.addressNoDeliver') );
//                return;
//            }

            if(!$this->validateProducts()) {
                $validator->errors()->add('products', trans('errors.wrongProductsTotal') );
                return;
            }

            if(!$this->validateGifts()) {
                $validator->errors()->add('gitfs', trans('errors.wrongGiftBonuses') );
                return;
            }

            if(intval($this->total) !== intval($this->deliverySum + $this->productsSum )) {
                $validator->errors()->add('total', trans('errors.wrongTotal') );
                return;
            }

//            // Check what user has enough bonuses to spend for gifts
//            if(!empty($this->giftBonuses)) {
//                if($this->giftBonuses > resolve('Bonuses')->getAvailableBonuses(Auth::user())) {
//                    $validator->errors()->add('gifts', trans('errors.notEnoughBonuses') );
//                }
//            }
        });
    }
}
