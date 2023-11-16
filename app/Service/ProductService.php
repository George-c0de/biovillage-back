<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\ProductModel;
use App\Searchers\ProductSearcher;
use App\Service\Images\ImageService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Search Service.
 *
 * @package App\Service\Section
 */
class ProductService
{

    /**
     * Update products data
     * @param ProductModel $product
     * @param array $data
     * @return mixed
     */
    private static function fill($product, $data)
    {
        // Tags
        $tags = $data['tags'] ?? [];
        if(empty($tags)) {
            $tags = [];
        }
        if(!is_array($tags)) {
            $tags = [$tags];
        }
        $data['tags'] = DbHelper::arrayToPgArray($tags);

        // Promo
        $data['promotion'] = $data['promotion'] ?? ProductModel::PROMO_NO;

        // Order
        $data['order'] = $data['order'] ?? ProductModel::DEFAULT_ORDER;

        // GroupId -> categorySectionId
        $data['categorySectionId'] = $data['groupId'];

        // Nutrition and other new str fields fix
        foreach( ['nutrition', 'composition', 'shelfLife'] as $f) {
            if(array_key_exists($f, $data) && is_null($data[$f]) ) {
                $data[$f] = '';
            }
        }

        $product->fill($data);

        return $data;
    }

    /**
     * Save images after save product
     * @param ProductModel $product
     * @param array $data
     */
    private static function saveProductImages($product, $data) {
        if (empty($data['image'])) {
            return;
        }
        ImageService::deleteByEntities(
            ProductModel::IMAGES_GROUP_NAME,
            $product->id
        );
        ImageService::save(
            $data['image'],
            ProductModel::IMAGES_GROUP_NAME,
            $product->id
        );
    }

    /**
     * Create product
     * @param $data - Product data
     * @return
     * @throws \ReflectionException
     */
    public static function createProduct($data) {

        $product = new ProductModel();

        if(Arr::has($data, 'netCostPerStep')){
            $data['netCostPerStep'] = $data['netCostPerStep'] * 100;
        }

        static::fill($product, $data);
        $product->save();
        static::saveProductImages($product, $data);
        return self::searchProductById($product->id);
    }

    /**
     * Update product data
     * @param ProductModel $product
     * @param $data
     * @return mixed
     * @throws \ReflectionException
     */
    public static function updateProduct($product, $data) {

        if(!$product instanceof ProductModel) {
            $product = ProductModel::findOrFail($product);
        }

        static::fill($product, $data);
        $product->save();
        static::saveProductImages($product, $data);
        return self::searchProductById($product->id);
    }

    /**
     * Delete product
     * @param ProductModel $product
     * @return bool
     * @throws \Exception
     */
    public static function delProduct($product) {
        if(!$product instanceof ProductModel) {
            $product = ProductModel::findOrFail($product);
        }
        $product->delete();
        return true;
    }

    /**
     * Search products
     * @param array $params. See ProductSearcher
     * @return array
     * @throws \ReflectionException
     */
    public static function search(array $params = [])
    {
        $ps = new ProductSearcher();

        return array_map(function($row) {

            // MainImage
            $row['imageSrc'] = Arr::get($row['images'], '0.1', '');
            if(!empty($row['imageSrc'])) {
                $row['imageSrc'] = Utils::fullUrl($row['imageSrc']);
            }
            unset($row['images']);

            // Certificates
            $row['certs'] = array_map(function($row) {
                return Utils::fullUrl($row[1]);
            }, $row['certificates']);
            unset($row['certificates']);

            return $row;

        }, $ps->search($params));
    }

    /**
     * Calc product count with search params
     * @param $params
     * @return int
     * @throws \ReflectionException
     */
    public static function getCount($params) {
        $ps = new ProductSearcher();
        return $ps->count($params);
    }

    /**
     * Search one product by id
     * @param $productId
     * @return mixed|null
     * @throws \ReflectionException
     */
    public static function searchProductById($productId) {
        $prods = static::search(['id' => $productId]);
        if(empty($prods)) {
            return null;
        }
        return $prods[0];
    }

    /**
     * Check products cost are same received cost
     * @param $prodsQty
     * @return bool
     * @throws \ReflectionException
     */
    public static function calcProductsSum($prodsQty) {
        if(empty($prodsQty)){
            return 0;
        }
        $sum = 0;
        $prods = static::search(['ids' => array_keys($prodsQty)]);
        foreach($prods as $prod) {
            $qty = $prodsQty[ $prod['id'] ] ?? 0;
            $sum += $prod['price'] * $qty;
        }
        return $sum;
    }
}
