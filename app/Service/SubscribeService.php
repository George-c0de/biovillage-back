<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Notify;
use App\Helpers\ResponseHelper;
use App\Mail\SubscribeMail;
use App\Models\SubscribeModel;
use Illuminate\Support\Facades\Mail;


/**
 * Class SubscribeService
 * @package App\Service
 */
class SubscribeService{

    const PROMO_CODE_1 = '2021FIRST';
    const PROMO_CODE_2 = '555';

    /**
     * Save subscribes email
     * @param $data
     */
    public static function subscribe($data) {
        $m = new SubscribeModel();
        $m->email = $data['email'];
        $m->createdAt = DbHelper::currTs();
        $m->save();
    }

    /**
     * Send mail with promo code
     * @param $email
     */
    public static function sendPromo($email) {
        Notify::sendMailImmediately($email, SubscribeMail::class, [
            'promoCode' => self::PROMO_CODE_2
        ]);
    }

}
