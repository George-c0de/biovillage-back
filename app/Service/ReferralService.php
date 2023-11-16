<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\SettingsModel as SM;
use App\Models\Auth\Client;

/**
 * Referral Service.
 *
 * @package App\Service
 */
class ReferralService
{


    /**
     * Set inviter client and check referrals count
     * @param $client
     * @param $referralCode
     */
    public function setClientInviter(Client $client, $referralCode) {
        $inviterId = self::convertReferralToId($referralCode);
        if(empty($inviterId)) {
            return;
        }
        $inviter = Client::find($inviterId);
        if(empty($inviter)) {
            return;
        }
        $client->invitedBy = $inviterId;
    }

    /**
     * Converts referral code to user ID.
     *
     * @param string $referralCode
     *
     * @return int
     */
    public function convertReferralToId($referralCode)
    {
        $referralCode = strtolower($referralCode);
        try {
            $id = base_convert($referralCode, 35, 10) ^ 0xFFFFFFFF;
            if($id >= DbHelper::MAX_INT) {
                $id = 0;
            }
        } catch (\Exception $e) {
            $id = 0; //Not existent client id
        }

        return $id;
    }

    /**
     * Converts user ID to referral code.
     *
     * @param integer $id
     *
     * @return string
     */
    public function convertIdToReferral($id)
    {
        return strtoupper(base_convert($id ^ 0xFFFFFFFF, 10, 35));
    }
}
