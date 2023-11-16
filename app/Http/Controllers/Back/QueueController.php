<?php

namespace App\Http\Controllers\Back;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Http\Requests\Admins\AddRequest;
use App\Http\Requests\Admins\DelRequest;
use App\Http\Requests\Admins\PasswordRequest;
use App\Http\Requests\Admins\ProfileRequest;
use App\Http\Requests\Admins\RolesRequest;
use App\Http\Requests\Admins\GetRequest;
use App\Http\Requests\Queue\ActionFailedJobRequest;
use App\Http\Requests\Queue\GetFailedJobRequest;
use App\Jobs\SendEmailJob;
use App\Models\Auth\RolesAndRights;
use App\Models\ClientLogModel;
use App\Models\FailedJobModel;
use App\Service\ClientLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Mail\HelloMail;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Notify;
use Illuminate\Support\Facades\Artisan;

/**
 * Реализация работы с заданиями очереди
 * Class QueueController
 * @package App\Http\Controllers\Back
 */
class QueueController extends BaseBackController {


    // Действия над проваленными заданиями
    const ACTION_DELETE = 'delete';
    const ACTION_RETRY = 'retry';

    /**
     * Статистика по череди
     */
    public function info() {
        return view('back.queue.info', [
            'queueSize' => Queue::size(),
            'errorsCount' => FailedJobModel::count()
        ]);
    }

    /**
     * Список проваленных заданий
     */
    public function failedJobs() {
        return view('back.queue.failed', [
            'jobs' => FailedJobModel::orderBy('failedAt', 'desc')->paginate()
        ]);
    }

    /**
     * Показываем подробности проваленного задания
     * @param GetFailedJobRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showFailed(GetFailedJobRequest $request) {
        return view('back.queue.show-failed', [
            'job' => FailedJobModel::findOrFail($request->get('id')),
        ]);
    }

    /**
     * Действия над проваленным заданием
     * Проваленное задание можно окончательно удалить или повторить
     * @param ActionFailedJobRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function actionFailed(ActionFailedJobRequest $request) {
        $action = $request->get('action');
        $jobIds = $request->get('ids');

        if($action == self::ACTION_DELETE) {
            FailedJobModel::whereIn('id', $jobIds)->delete();
        } else if ($action == self::ACTION_RETRY){
            foreach ($jobIds as $jobId) {
                Artisan::call('queue:retry', ['id' => $jobId] );
            }
        } else {
            Utils::raise('Wrong action');
        }

        return redirect()
            ->route('failedJobs')
            ->with([
                'deleteSuccess' => $action == self::ACTION_DELETE,
                'repeatSuccess' => $action == self::ACTION_RETRY,
            ]);
    }

}