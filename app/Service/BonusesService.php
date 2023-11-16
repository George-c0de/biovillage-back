<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\Auth\Client;
use App\Models\GiftModel;
use App\Models\OrderModel as OM;
use App\Models\OrderModel;
use App\Models\SettingsModel as SM;
use App\Searchers\BonusesSearcher;
use App\Searchers\ClientSearcher;
use App\Searchers\GiftSearcher;
use App\Service\Images\ImageService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Bonuses service
 *
 * @package App\Service\BonusesService
 */
class BonusesService
{

    /**
     * Get client bonuses
     * @param $client - ID or instace
     * @return int
     * @throws \Exception
     */
    public function getClientBonuses($client)
    {
        if ($client instanceof Client) {
            $info = $client->toArray();
        } else {
            $s = new ClientSearcher();
            $info = $s->searchOne($client);
            if (empty($info)) {
                Utils::raise('Client not exists');
            }
        }

        return intval($info['bonuses']);
    }

    /**
     * Get extended bonuses
     * @param $client
     * @return mixed
     */
    public function getExtClientBonuses($client) {

        if($client instanceof Client) {
            $client = $client->id;
        }

        $s = new BonusesSearcher();
        return $s->calcLockedBonuses([
            'clientId' => $client,
            'orderStatuses' => array_diff(OM::STATUES, OM::ENDED_STATUSES)
        ]);
    }

    /**
     * Receive locked bonuses for client
     * @param Client | int $client
     * @return int
     */
    public function getClientLockedBonuses($client) {

        if($client instanceof Client) {
            $client = $client->id;
        }

        $s = new BonusesSearcher();
        $row = $s->calcLockedBonuses([
            'clientId' => $client,
            'orderStatuses' => array_diff(OM::STATUES, OM::ENDED_STATUSES)
        ]);
        return intval($row['lockedBonuses'] ?? 0);
    }

    /**
     * Receive locked bonuses for order
     * @param OrderModel $order
     * @return int
     */
    public function getOrderLockedBonuses($order) {
        $s = new BonusesSearcher();
        $row = $s->calcLockedBonuses([
            'orderId' => $order->id,
            'orderStatuses' => array_diff(OM::STATUES, OM::ENDED_STATUSES)
        ]);
        return intval($row['lockedBonuses'] ?? 0);
    }

    /**
     * Receive locked bonuses for order
     * @param OrderModel $order
     * @return int
     */
    public function getOrderBonusesToWriteOff($order) {

        $s = new BonusesSearcher();
        $row = $s->calcLockedBonuses([
            'orderId' => $order->id,
            'orderStatuses' => array_diff(OM::STATUES, OM::ENDED_STATUSES)
        ]);
        return intval($row['writeOffBonuses'] ?? 0);
    }

    /**
     * Write off bonuses
     * @param OrderModel | int $order
     */
    public function writeOffBonusesForOrder($order) {
        $order = OrderModel::orderInstance($order);
        $offBonuses = $this->getOrderBonusesToWriteOff($order);
        if($offBonuses > 0) {
            $client = $order->client();
            $client->bonuses -= $offBonuses;
            $client->save();
        }
    }

    /**
     * Add bonus for new client if invited
     * Client invitedBy field must be fill
     * @param $client
     */
    public function addBonusIfInvited($client) {
        if(empty($client->invitedBy)) {
            return;
        }
        $referralBonus = SettingsService::getSettingValue(SM::SETTING_REFERAL_BONUS);
        $maxReferralUsers = SettingsService::getSettingValue(SM::SETTING_MAX_REFERALS);
        if(ClientService::invitesCount($client->invitedBy) < $maxReferralUsers) {
            $client->bonuses += $referralBonus;
            Log::info('REF BONUS', [
                'by' => 0,
                'c' => $client->id,
                'b' => $referralBonus,
                'mru' => $maxReferralUsers
            ]);
        }
    }

    /**
     * Add bonuses
     *  Bonus for order
     *  Bonus for referral
     * @param OrderModel | int $order
     */
    public function addBonusesForOrderAndReferral($order) {
        $order = OrderModel::orderInstance($order);
        $client = $order->client();
        $clientOrdersCount = OrderService::getClientCompletedOrdersCount($client);

        // Bonuses for referral if client invited and finished order
        if(!empty($client->invitedBy)) {
            if($clientOrdersCount == 0) {
                $referralBonus = SettingsService::getSettingValue(SM::SETTING_REFERAL_BONUS);
                $maxReferralUsers = SettingsService::getSettingValue(SM::SETTING_MAX_REFERALS);
                if(ClientService::invitesCount($client->invitedBy) < $maxReferralUsers) {
                    // Add bonus for inviter
                    $this->addBonusesToClient($client->invitedBy, $referralBonus);

                    Log::info('REF BONUS', [
                        'by' => 1,
                        'c' => $client->invitedBy,
                        'b' => $referralBonus,
                        'mru' => $maxReferralUsers
                    ]);
                }
            }
        }

        // Bonus for order count
        $purchaseSum = OrderService::calcPurchasedProductsSum($order);
        if($clientOrdersCount === 0) {
            $cashbackPercent = SettingsService::getSettingValue(SM::SETTING_CASHBACK_FIRST_ORDER);
            if(!empty($cashbackPercent)) {
                $bonus = intval(floor($purchaseSum * ( $cashbackPercent / 100 )));
                $client->bonuses += $bonus;

                Log::info('ORDER BONUS', [
                    'f' => 1,
                    'c' => $client->id,
                    'b' => $bonus,
                    'ps' => $purchaseSum,
                    'cp' => $cashbackPercent
                ]);
            }
        } else {
            $cashbackPercent = SettingsService::getSettingValue(SM::SETTING_CASHBACK_EACH_ORDER);
            if(!empty($cashbackPercent)) {
                $bonus = intval(floor($purchaseSum * ( $cashbackPercent / 100 )));
                $client->bonuses +=  $bonus;

                Log::info('ORDER BONUS', [
                    'f'  => 0,
                    'c' => $client->id,
                    'b' => $bonus,
                    'ps' => $purchaseSum,
                    'cp' => $cashbackPercent
                ]);
            }
        }
        $client->save();
    }

    /**
     * Calc available client bonuses
     * @param $client
     * @return int
     */
    public function getAvailableBonuses($client) {
        return intval($client->bonuses) - $this->getClientLockedBonuses($client);
    }

    /**
     * Add bonuses to client. Fast and accurately
     * @param $clientId
     * @param $addBonuses
     */
    public function addBonusesToClient($clientId, $addBonuses) {
        DB::statement('
            UPDATE clients SET bonuses = bonuses + ? WHERE id = ?
        ', [$addBonuses, $clientId]);
    }
}
