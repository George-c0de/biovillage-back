<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 01.02.2021
 * Time: 16:37
 */

namespace App\Service\PaymentGateway;


interface IPaymentNotificationValidator
{

    /**
     * Notification rules
     * @return array
     */
    public function rules() : array;

    /**
     * Addition validation logic
     * @param $request
     * @param $validator
     * @return mixed
     */
    public function validate($request, $validator);

}