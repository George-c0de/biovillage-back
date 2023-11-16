<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Notify;
use App\Http\Requests\Order\StoreRequest;
use App\Mail\OrderMail;

use App\Service\StorageService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Контроллер для отправки писем
 */
class MailController extends BaseApiController {
    /**
     * Отправка заказа
     * @param StoreRequest $request
     */
    public function order(StoreRequest $request) {
        $email = 'test@mail.com';
        $validated = $request->validated();
        $filePaths = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $path = StorageService::saveOriginName($file, 'order');
                $filePaths[] = public_path() . Storage::url($path);
            }
        }
        unset($validated['files']);

        Notify::sendMail($email, OrderMail::class, [
            'data' => $validated,
            'filePaths' => $filePaths,
        ]);
    }
}
