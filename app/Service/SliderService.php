<?php namespace App\Service;

use App\Helpers\Utils;
use App\Models\SliderModel;
use Illuminate\Support\Facades\Storage;

/**
 * Slider Service
 *
 * @package App\Service
 */
class SliderService
{
    /**
     * Returns slider with image `src` field.
     *  id
     *  active
     *
     * @param array $params
     * @return array
     */
    public static function search(array $params = [])
    {
        $q = SliderModel::select([
                'sliders.id',
                'sliders.name',
                'sliders.description',
                'sliders.bgColor',
                'sliders.order',
                'sliders.active',
                'images.src as imageSrc',
            ])
            ->join('images', function ($join) {
                $join->on('sliders.id', '=', 'images.entityId')
                    ->where('images.groupName', SliderModel::GROUP_NAME)
                    ->whereNull('images.deletedAt');
            })
            ->whereNull('sliders.deletedAt')
            ->orderBy('sliders.order')
            ->orderBy('sliders.name');

        if (!empty($params['id'])) {
            $q->where('sliders.id', $params['id']);
        }
        if(!empty($params['active'])) {
            $q->where('sliders.active', true);
        }

        // Result
        return $q->get()->map(function($item) {
            $item->imageSrc = Utils::fullUrl($item->imageSrc);
            return $item;
        })->toArray();
    }

    /**
     * Get slider info by id
     * @param $id
     * @return mixed
     */
    public static function getById($id) {
        $sliders = self::search(['id' => $id]);
        if(!empty($sliders)) {
            return $sliders[0];
        }
        return [];
    }

    /**
     * Return sliders list for mobile client
     */
    public static function getClientSliders() {
        return static::search(['active' => true]);
    }
}
