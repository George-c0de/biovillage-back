<?php namespace App\Http\Requests;

use App\Helpers\DbHelper;

class CheckIdRequest extends BaseApiRequest
{
    function all($keys = null)
    {
        $data = parent::all($keys);
        $idParam = $this->route('id');

        if (!is_null($idParam)) {
            $data['id'] = $idParam;
        }

        return $data;
    }

    public function attributes()
    {
        $attrs = parent::attributes();
        return array_merge($attrs, ['id' => trans('validation.id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT,
        ];
    }
}
