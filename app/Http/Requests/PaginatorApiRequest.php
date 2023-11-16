<?php

namespace App\Http\Requests;

use App\Helpers\Utils;

class PaginatorApiRequest extends BaseApiRequest {
    const OFFSET = 0;
    const LIMIT = 15;

    public function rules() {
        return [
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ];
    }

    /**
     * Перед валидацией подставляем занчения по дефолту
     */
    protected function prepareForValidation() {
        $data = $this->request->all();
        $data = Utils::arrayValuesEmpty($data, (string) 0);

        if (!$this->request->has('offset') || !$data['offset']) {
            $data['offset'] = (string) self::OFFSET;
        }

        if (!$this->request->has('limit') || !$data['limit']) {
            $data['limit'] = (string) self::LIMIT;
        }

        $this->merge($data);
    }
}
