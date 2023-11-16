<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Helpers\Notify;
use App\Helpers\Utils;
use App\Http\Requests\Client\BonusesRequest;
use App\Http\Requests\Client\ListRequest;
use App\Http\Requests\Client\ShowRequest;
use App\Http\Requests\Client\UpdateBirthdayRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Mail\UpdateBirthdayMail;
use App\Service\BonusesService;
use App\Service\PhoneService;
use App\Service\ClientService;
use App\Service\GroupService;
use App\Service\Phone;
use App\Models\AddressModel;
use App\Jobs\SendSmsJob;
use App\Models\SettingsModel;
use App\Helpers\DbHelper;
use App\Models\Auth\Client;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Client\ClientVerifyRequest;
use App\Http\Requests\Client\ClientRegisterRequest;

class ClientController extends BaseApiController
{
    /**
     * @var string
     */
    const LOGIN_TYPE = 'login';

    /**
     * @var string
     */
    const REGISTER_TYPE = 'register';

    /**
     * Cache lifetime in seconds.
     *
     * @var integer
     */
    const CACHE_LIFETIME = 600;

    /**
     * Login/Register handler
     *
     * @param ClientRegisterRequest $request
     *
     * @return JsonResponse
     */
    public function registerRequest(ClientRegisterRequest $request)
    {
        $validated = $request->validated();

        $phone = Phone::prepare($validated['phone']);
        if(is_null($phone)) {
            return ResponseHelper::errorKey(
                'phone', trans('errors.wrongPhoneNumber')
            );
        }

        $forTest = PhoneService::isTestPhone($phone);
        $code = ClientService::genSmsCode($forTest);
        $referral = $validated['referral'] ?? '';
        $encryptedString = ClientService::serializeClientData(
            $phone, $code, $referral
        );

        Cache::put($phone, $encryptedString, self::CACHE_LIFETIME);

        if(!$forTest) {
            Notify::sendSms($phone, 'sms.sms', compact('code'));
        }

        return ResponseHelper::success(['Code generated and sent successfully']);
    }

    /**
     * Login/Register verify.
     *
     * @param ClientVerifyRequest $request
     *
     * @return JsonResponse
     */
    public function registerVerify(ClientVerifyRequest $request)
    {
        // Get params
        $validated = $request->validated();
        $phone = Phone::prepare($validated['phone']);
        if(is_null($phone)) {
            return ResponseHelper::errorKey(
                'phone', trans('errors.wrongPhoneNumber')
            );
        }

        // Retrieve cache data by phone
        if (!Cache::has($phone)) {
            return ResponseHelper::errorKey(
                'phone',
                trans('errors.wrongPhoneNumber'),
                Response::HTTP_NOT_FOUND
            );
        }
        list($phone, $code, $referral ) = ClientService::deserializeClientData(
            Cache::get($phone)
        );
        if(empty($phone)) {
            if(is_null($phone)) {
                return ResponseHelper::errorKey(
                    'phone', trans('errors.wrongPhoneNumber')
                );
            }
        }

        if ($validated['code'] !== $code) {
            return ResponseHelper::errorKey('code', 'Invalid confirmation code.');
        }
        Cache::forget($phone);

        // Login and make access token
        $client = Client::where('phone', $phone)->first();
        if (empty($client)) {
            $client = ClientService::createClient($phone, $referral);
        }
        ClientService::clientLogin(
            $client,
            $validated['platform'] ?? Client::PLATFORM_NA
        );

        //
        $token = $client->createToken('clientAuthToken')->toArray();
        if(empty($token['plainTextToken'])) {
            return ResponseHelper::error('Empty plain text token');
        }

        return ResponseHelper::success(array_merge(
            ClientService::myInfo($client, true),
            [
                'token' => $token['plainTextToken']
            ]
        ));
    }

    /**
     * User logout handler.
     *
     * @return JsonResponse
     */
    public function logout() {
        $client = Auth::user();
        if(!empty($client)) {
            $tokens = $client->tokens();
            if(!empty($tokens)) {
                $tokens->where('id', $client->currentAccessToken()->id)
                    ->delete();
            }
        }

        return ResponseHelper::ok();
    }

    /**
     * Get full user info
     */
    public function me() {
        return ResponseHelper::success(
            ClientService::myInfo(Auth::user())
        );
    }

    /**
     * Update client
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request) {
        return ResponseHelper::success(
            ClientService::myInfo(
                ClientService::updateClientInfo(
                    Auth::user(),
                    $request->validated()
                ),
                true
            )
        );
    }

    /**
     * Update client
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function updateViaManager(UpdateRequest $request) {
        return ResponseHelper::success(
            ClientService::myInfo(
                ClientService::updateClientInfo(
                    Client::clientInstance($request->id),
                    $request->validated()
                ),
                true
            )
        );
    }

    /**
     * TODO: Delete it
     * Request to update birthday
     * @param UpdateBirthdayRequest $request
     * @return JsonResponse
     */
    public function updateBirthday(UpdateBirthdayRequest $request) {
        Notify::sendMail(env('NOTIFICATIONS_EMAIL'), UpdateBirthdayMail::class, [
            'clientId' => Auth::id(),
            'birthday' => $request->birthday
        ]);
        return ResponseHelper::ok();
    }

    /**
     * Delete client avatar
     */
    public function deleteAvatar() {
        ClientService::deleteAvatar(Auth::user());
        return ResponseHelper::ok();
    }

    /**
     * Search clients by range of conditions
     * @param ListRequest $request
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function list(ListRequest $request) {
        $params = $request->validated();

        $pager = new Paginator(
            ClientService::count($params),
            $params['perPage'] ?? Client::DEFAULT_PER_PAGE,
            $params['page'] ?? 1
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();

        return ResponseHelper::success([
            'clients' => ClientService::search($params),
            'pager' => $pager->toArray()
        ]);
    }


    /**
     * @param ShowRequest $request
     * @param $id
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function info(ShowRequest $request) {
        $validation = $request->validated();
        return ResponseHelper::success(
            ClientService::myInfo($validation['id'])
        );
    }

    /**
     * Charge bonuses
     * @param BonusesRequest $request
     * @return JsonResponse
     */
    public function bonuses(BonusesRequest $request) {
        $data = $request->validated();
        $client = Client::findOrFail($data['id']);
        $client->bonuses = $request->bonuses;
        $client->save();

        return $this->info($request);
    }
}
