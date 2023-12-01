<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\Auth\Client;
use App\Searchers\ClientSearcher;
use App\Service\Images\ImageService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;

/**
 * Working for clients
 *
 * Class ClientService
 * @package App\Service
 */
class ClientService
{

    // SMS Code for debug
    const DEBUG_CODE = '11111';

    /**
     * Gen sms code for check client phone
     * @param bool $forDebug
     * @return int|string
     */
    public static function genSmsCode($forDebug = false)
    {
        return App::environment('local') || $forDebug
            ? self::DEBUG_CODE
            : rand(10000, 99999);
    }

    /**
     * Превращаем данные клиента в строку для сохранения в кеше
     * @param $phone
     * @param $code
     * @param $referal
     * @return string
     */
    public static function serializeClientData($phone, $code, $referal)
    {
        return Crypt::encryptString(implode('|', [
            $phone,
            $code,
            $referal
        ]));
    }

    /**
     * Превращаем данные клиента в строку для сохранения в кеше
     * @param $serialized
     * @return array
     */
    public static function deserializeClientData($serialized)
    {
        try {
            $decryptedString = Crypt::decryptString($serialized);
        } catch (\Exception $e) {
            return ['', '', ''];
        }

        return explode('|', $decryptedString);
    }

    /**
     * Создание новго клиента при логине
     * @param $phone
     * @param $referralCode
     * @return Client
     */
    public static function createClient($phone, $referralCode)
    {
        $client = new Client();
        $client->phone = $phone;
        $client->allowMailing = true;
        resolve('Referral')->setClientInviter($client, $referralCode);
        resolve('Bonuses')->addBonusIfInvited($client);
        $client->save();
        return $client;
    }

    /**
     * Client login event
     * @param $client
     * @param $platform - See Client::PLATFORMS
     */
    public static function clientLogin($client, $platform)
    {
        $client->lastLoginAt = DbHelper::currTs();
        $client->lastPlatform = $platform ?? Client::PLATFORM_NA;
        $client->save();
    }

    /**
     * Get full info for client
     * @param Client $client
     * @param bool $needRefresh
     * @return array
     */
    public static function myInfo($client, $needRefresh = false, $realBonuses = false)
    {

        $client = Client::clientInstance($client);
        if ($needRefresh) {
            $client->refresh();
        }
        $info = $client->toArray();
        $info['lastLoginAt'] = locale()->dbDtToDtStr($info['lastLoginAt']);
        $info['createdAt'] = locale()->dbDtToDtStr($info['createdAt']);
        $info['updatedAt'] = locale()->dbDtToDtStr($info['updatedAt']);
        $info['birthday'] = locale()->dbDateToDateStr($info['birthday']);
        $info['phone'] = Phone::toShow($info['phone']);
        $info['addresses'] = AddressService::searchClientAddresses($client);
        $info['referralCode'] = resolve('Referral')->convertIdToReferral($client->id);
        $info['avatar'] = Utils::fullUrl($info['avatar']);
        $info['lastPayMethod'] = PaymentService::getClientLastPayMethod($client->id);
        $info['bonuses'] = resolve('Bonuses')->getAvailableBonuses($client);
        $info['lockedBonuses'] = resolve('Bonuses')->getClientLockedBonuses($client);
        $info['allBonuses'] = $client['bonuses'];

        // Hide important values
        unset($info['invitedBy']);

        return $info;
    }

    /**
     * Обновление данных клиента
     * @param $client
     * @param $data
     *  name
     *  email
     *  allowMailing
     *  birthday
     *
     * @return mixed
     */
    public static function updateClientInfo(Client $client, array $data = [])
    {

        // Fill data
        $data['allowMailing'] = boolval($data['allowMailing']
            ?? $client->allowMailing);

        if (!empty($data['birthday'])) {
            $data['birthday'] = locale()->dateToDbStr($data['birthday']);
        }
        $client->fill($data);

        // Save images
        if (!empty($data['avatar'])) {

            ImageService::deleteByEntities(
                Client::IMAGE_GROUP_NAME,
                $client->id
            );

            $im = ImageService::save(
                $data['avatar'],
                Client::IMAGE_GROUP_NAME,
                $client->id
            );
            $client->avatar = $im['src'];
        }

        // Save all
        $client->save();

        return $client;
    }

    /**
     * Очищение данных клиента
     * @param $client
     */
    public static function clearClientInfo(Client $client)
    {
        // Fill data
        $data['avatar'] = null;
        $data['name'] = null;
        $data['birthday'] = null;
        $data['email'] = null;
        $data['phone'] = PhoneService::buildPhoneWithPrefix($client->phone);

        $client->forceFill($data);

        ImageService::deleteByEntities(
            Client::IMAGE_GROUP_NAME,
            $client->id
        );
        // Save all
        $client->save();

        return $client;
    }


    /**
     * Delete client avatar
     * @param $client
     */
    public static function deleteAvatar($client)
    {
        ImageService::deleteByEntities(
            Client::IMAGE_GROUP_NAME,
            $client->id
        );
        $client->avatar = '';
        $client->save();
    }

    /**
     * Search clients
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public static function search($params = [])
    {
        $s = new ClientSearcher();
        return $s->search($params);
    }

    /**
     * Search clients
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public static function searchOne($params = [])
    {
        $s = new ClientSearcher();
        $res = $s->search($params);
        if (empty($res)) {
            return null;
        }
        return $res[0];
    }

    /**
     * Calc client count
     * @param array $params
     * @return int
     * @throws \ReflectionException
     */
    public static function count($params = [])
    {
        $s = new ClientSearcher();
        return $s->count($params);
    }

    /**
     * Calc invites count
     * @param $clientId
     * @return int
     */
    public static function invitesCount($clientId)
    {
        $s = new ClientSearcher();
        return $s->count(['invitedById' => $clientId]);
    }


}