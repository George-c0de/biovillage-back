<?php

namespace App\Http\Requests\Admin;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Admin;
use App\Service\PhoneService;
use App\Service\AdminRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRequest extends BaseApiRequest
{
    public function rules()
    {
        return [
            'id' => sprintf(
                'required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new Admin())->getTable()
            ),
            'name' => 'nullable|string|min:2|max:255',
            'phone' => 'nullable|string|min:10|max:20',
            'password' => 'nullable|string|min:2|max:255',
            'roles' => 'nullable|array',
            'roles.*' => [
                'nullable',
                Rule::in(AdminRole::ROLES),
            ],
        ];
    }

    /**
     * @param Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator){
            $admin = Auth::user();
            if(!AdminRole::hasRole($admin, [AdminRole::SUPERADMIN_ROLE]) && $admin['id'] != $this['id']){
                $validator->errors()->add('role', 'У вас недостаточно прав для данной операции');
            }

            $phone = PhoneService::preparePhoneNumber($this['phone']);
            $existsAdmin = Admin::where('phone', $phone)->first();

            if($existsAdmin && $existsAdmin['id'] != $this['id']) {
                $validator->errors()->add('phone', 'Админ с тарим телефоном уже существует');
            }
        });
    }

    /**
     * Перед валидацией подставляем занчения
     */
    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id']
        ]);
    }
}
