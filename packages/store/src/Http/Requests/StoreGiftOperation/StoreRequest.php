<?php

namespace Packages\Store\Http\Requests\StoreGiftOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\GiftModel;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Models\StorePlaceModel;

class StoreRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'storeId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),
            'comment' => 'nullable|string|min:10|max:1000',

            'files' => 'nullable|array',
            'files.*' => 'required|array',
            'files.*.src' => 'required|file|max:'.StoreOperationModel::MAX_FILE_SIZE,
            'files.*.name' => 'required|string',

            // Contents
            'contents' => 'nullable|array',
            'contents.*' => 'nullable|array',
            'contents.*.giftId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new GiftModel())->getTable()
            ),
            'contents.*.realUnits' => 'required|integer|min:1|max:'.DbHelper::MAX_INT,
            'contents.*.storePlaceId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                'storePlaces'
            ),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator){
            $store = StoreModel::find($this['storeId']);
            if($store && $store['type'] !== StoreModel::TYPE_GIFT){
                $validator->errors()->add('storeType', 'Тип склада не для подарков');
            }

            $placesOrigin = StorePlaceModel::select('id')
                ->where('storeId', $this['storeId'])
                ->pluck('id')
                ->toArray();

            if(Arr::has($this, 'contents')){
                foreach ($this['contents'] as $content) {
                    if(
                        Arr::has($content, 'storePlaceId') &&
                        Arr::has($content, 'storeId') &&
                        !in_array($content['storePlaceId'], $placesOrigin)
                    ){
                        $validator->errors()->add('storePlaceId', 'Место '.$content['storePlaceId'].' не принадлежит складу '.$this['storeId']);
                    }
                }
            }
        });
    }
}
