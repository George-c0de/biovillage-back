<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\GiftModel;
use App\Searchers\GiftSearcher;
use App\Service\Images\ImageService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Gift service
 *
 * @package App\Service\Section
 */
class GiftService
{

    /**
     * Update products data
     * @param GiftModel $product
     * @param array $data
     * @return mixed
     */
    private function fill($product, $data)
    {
        $data['order'] = $data['order'] ?? GiftModel::DEFAULT_ORDER;
        Utils::nullToStrInArray(['description','composition','shelfLife',], $data);
        $product->fill($data);
        return $data;
    }

    /**
     * Save images after save gift
     * @param GiftModel $gift
     * @param array $data
     */
    private function saveGiftImages($gift, $data) {
        if (empty($data['image'])) {
            return;
        }
        ImageService::deleteByEntities(
            GiftModel::IMAGES_GROUP_NAME,
            $gift->id
        );
        ImageService::save(
            $data['image'],
            GiftModel::IMAGES_GROUP_NAME,
            $gift->id
        );
    }

    /**
     * Create gift
     * @param $data - Gift data
     * @return array - Gift data
     * @throws \ReflectionException
     */
    public function createGift($data) {

        $gift = new GiftModel();
        static::fill($gift, $data);
        $gift->createdAt = DbHelper::currTs();
        $gift->updatedAt = DbHelper::currTs();
        $gift->save();
        $this->saveGiftImages($gift, $data);
        return $this->searchGiftById($gift->id);
    }

    /**
     * Update gift
     * @param GiftModel $gift
     * @param $data
     * @return mixed
     * @throws \ReflectionException
     */
    public function updateGift($gift, $data) {

        if(!$gift instanceof GiftModel) {
            $gift = GiftModel::findOrFail($gift);
        }

        $this->fill($gift, $data);
        $gift->save();
        $this->saveGiftImages($gift, $data);
        return $this->searchGiftById($gift->id);
    }

    /**
     * Delete gift
     * @param GiftModel | int $gift
     * @return bool
     * @throws \Exception
     */
    public static function delete($gift) {
        if(!$gift instanceof GiftModel) {
            $gift = GiftModel::findOrFail($gift);
        }
        $gift->delete();
        return true;
    }

    /**
     * Search products
     * @param array $params. See GiftSearcher
     * @return array
     * @throws \ReflectionException
     */
    public static function search(array $params = [])
    {
        $ps = new GiftSearcher();

        return array_map(function($row) {

            // MainImage
            $row['imageSrc'] = Arr::get($row['images'], '0.1', '');
            if(!empty($row['imageSrc'])) {
                $row['imageSrc'] = Utils::fullUrl($row['imageSrc']);
            }
            unset($row['images']);

            return $row;
        }, $ps->search($params));
    }

    /**
     * Search one gift
     * @param $giftId
     * @return mixed|null
     * @throws \ReflectionException
     */
    public function searchGiftById($giftId) {
        $gifts = $this->search(['id' => $giftId]);
        if(empty($gifts)) {
            return null;
        }
        return $gifts[0];
    }

    /**
     * Calc gift bonuses total
     * @param $giftsQty
     * @return float|int
     */
    public function calcGiftsBonuses($giftsQty) {
        if(empty($giftsQty)){
            return 0;
        }
        $sum = 0;
        $gifts = $this->search(['ids' => array_keys($giftsQty)]);
        foreach($gifts as $gift) {
            $qty = $giftsQty[ $gift['id'] ] ?? 0;
            $sum += $gift['price'] * $qty;
        }
        return $sum;
    }


}
