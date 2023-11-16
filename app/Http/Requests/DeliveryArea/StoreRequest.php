<?php namespace App\Http\Requests\DeliveryArea;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class StoreRequest extends BaseApiRequest
{

    /**
     * Return base rules for validation
     * @return array
     */
    public static function getBaseRules() {
        return [
            'names' => 'array',
            'names.*' => 'string|min:3|max:255',
            'colors' => 'array',
            'colors.*' => [ 'string', 'regex:/^#[a-fA-F0-9]{6}$/' ],
            'polygons' => 'array',
            'polygons.*' => ['string', 'max:5024', 'regex:/^\((?:\([\d\.]+,[\d+\.]+\),?)+\)$/'],
            'prices' => 'array',
            'prices.*' => 'int|min:0|max:' . DbHelper::MAX_INT,
            'actives' => 'array',
            'actives.*' => 'boolean',
            'deliveryFreeSums' => 'array',
            'deliveryFreeSums.*' => 'int|min:0|max:' . DbHelper::MAX_INT,
        ];
    }

    /**
     * Check array sizes
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Errors already exists
            if($validator->errors()->count() > 0) {
                return;
            }

            // Check arrays count
            $nameCnt = count($this->names ?? []);
            $colorCnt = count($this->colors ?? []);
            $polygonCnt = count($this->polygons ?? []);
            $priceCnt = count($this->prices ?? []);
            $activesCnt = count($this->actives ?? []);
            $freeDeliveryCnt = count($this->deliveryFreeSums ?? []);
            if($nameCnt != $colorCnt or $nameCnt != $polygonCnt
                or $nameCnt != $priceCnt or $activesCnt != $nameCnt or
                $freeDeliveryCnt != $nameCnt) {
                $validator->errors()->add(
                    'name',
                    trans('errors.daCntNamesColorCoords')
                );
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return self::getBaseRules();
    }
}
