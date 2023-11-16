<?php namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\DestroyRequest;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\ShowRequest;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\Service\PackageService;
use App\Service\PhoneService;
use App\Service\AdminService;
use App\Service\Phone;
use App\Helpers\DbHelper;
use App\Models\Auth\Admin;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\LoginRequest;

class AdminController extends BaseApiController
{

    /**
     * Login handler.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('phone', 'password');

        $credentials['phone'] = Phone::prepare($credentials['phone']);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Admin::where('phone', $credentials['phone'])->first();
            if (null !== $admin) {
                $token = $admin->createToken('adminToken');

                return ResponseHelper::success([
                    'name' => $admin->name,
                    'roles' => DbHelper::pgArrayToArray($admin->roles),
                    'token' => $token->plainTextToken,
                    'packages' => PackageService::getPackages(),
                ]);
            }
        }

        return ResponseHelper::errorKey('phone', 'Login or password wrong');
    }

    /**
     * @WARNING: NOT IN USE YET!
     * Logout handler
     *
     * @return JsonResponse
     */
    public function logout() {
        $admin = Auth::user();
        if(!empty($admin)) {
            $tokens = $admin->tokens();
            if(!empty($tokens)) {
                $tokens->where(
                    'id',
                    $admin->currentAccessToken()->id
                )->delete();
            }
        }
        return ResponseHelper::ok();
    }

    /**
     * Returns administrators list.
     *
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request)
    {
        return ResponseHelper::success(
            AdminService::list([
                'sort' => $request->validatedAttribute(
                    'sort', AdminService::SORT_NAME
                ),
                'sortDirect' => $request->validatedAttribute(
                    'sortDirect', 'asc'
                )
            ])
        );
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(StoreRequest $request) {
        return ResponseHelper::success(
            AdminService::create($request->validated())
        );
    }

    /**
     * @param ShowRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(ShowRequest $request, $id) {
        $validated = $request->validated();
        return ResponseHelper::success(
            AdminService::get($validated['id'])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, $id) {
        $validated = $request->validated();

        if(Arr::has($validated, 'password') && $validated['password']) $validated['password'] = Hash::make($validated['password']);
        if($validated['roles']) $validated['roles'] = DbHelper::arrayToPgArray($validated['roles']);
        if($validated['phone']) $validated['phone'] = PhoneService::preparePhoneNumber($validated['phone']);

        $admin = Admin::find($validated['id']);
        unset($validated['id']);
        $admin->fill($validated);
        if(Arr::has($validated, 'password') && $validated['password']) $admin->password = $validated['password'];
        $admin->saveOrFail();

        return ResponseHelper::success(AdminService::get($admin->id));
    }

    /**
     * @param DestroyRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, $id) {
        $validated = $request->validated();
        $admin = Admin::find($validated['id']);
        $admin->delete();

        return ResponseHelper::ok();
    }
}
