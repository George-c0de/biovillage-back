<?php namespace App\Http\Controllers\Api;

use App\Http\Requests\Billing\PaymentListenerRequest;
use App\Service\BillingService;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Billing controller
 */
class BillingController extends BaseApiController
{

    /**
     * @var BillingService
     */
    private $billing;

    /**
     * BillingController constructor.
     * @param BillingService $billing
     */
    public function __construct(BillingService $billing)
    {
        $this->billing = $billing;
    }

    /**
     * Payment listener
     * @param PaymentListenerRequest $request
     * @return JsonResponse
     */
    public function paymentListener(PaymentListenerRequest $request) {
        $gateway = $this->billing->gateway();
        $context = $gateway->getNotificationContext($request->validated());

        try {
            $gateway->processNotification($context);
        } catch (\Exception $e) {
            Log::debug('Notify error', [$e->getMessage()]);
            throw $e;
        }

        return ResponseHelper::ok();
    }
}
