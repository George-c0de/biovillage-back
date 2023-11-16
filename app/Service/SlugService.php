<?php

namespace App\Service;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс для формирования уникального слага
 */
class SlugService {
    private const RUS = "а|б|в|г|д|е|ё|ж|з|и|й|к|л|м|н|о|п|р|с|т|у|ф|х|ц|ч|ш|щ|ъ|ы|ь|э|ю|я";
    private const ENG = "a|b|v|g|d|e|yo|zh|z|i|j|k|l|m|n|o|p|r|s|t|u|f|h|c|ch|sh|shh||y||e|yu|ya";

    private const SLUG_MAX_LENGTH = 255;
    private const SLUG_KEY_NAME = 'slug';

    /**
     * @var array
     */
    private static $rus;

    /**
     * @var array
     */
    private static $eng;

    /**
     * Транслитерация текста
     * @param string $text
     * @return string
     */
    private static function translit(string $text): string {
        if (!self::$rus) {
            self::$rus = explode('|', static::RUS);
        }
        if (!self::$eng) {
            self::$eng = explode('|', static::ENG);
        }
        return str_replace(self::$rus, self::$eng, $text);
    }

    /**
     * Преобразовать текст $text в slug
     * @param string $text
     * @param string $delimiter
     * @return string
     */
    public static function createSlug(string $text, $delimiter = '_'): string {
        $text = mb_strtolower($text);
        $text = self::translit($text);
        $text = preg_replace('~[\s' . $delimiter . ']+~', $delimiter, trim($text));
        $text = preg_replace('~[^a-z0-9' . $delimiter . ']+~', '', $text);
        $text = preg_replace('~[' . $delimiter . ']+~', $delimiter, $text);
        return trim($text, '-');
    }


    /**
     * Формирование уникального слага для модели
     * @param $model
     * @param string $slug
     * @param bool $isTranslit
     * @return string|string[]|null
     */
    public static function uniqueSlug($model, string $slug, $isTranslit = true) {
        $slug = $isTranslit ? self::createSlug($slug) : $slug;

        if ($model instanceof Model) {
            $builder = $model->where($model->getKeyName(), '!=', $model->getKey());
        } else {
            $builder = new $model;
            $model = new $model;
        }

        $slug = Str::limit(Str::slug($slug), self::SLUG_MAX_LENGTH, '');

        // Если слаг не существует - возвращаем исходную строку
        if (!$builder->where(self::SLUG_KEY_NAME, $slug)->exists()) {
            return $slug;
        }


        // Удаляем число в начале строки
        $slug = preg_replace('/^(\d+)-(\w+)$/', '$2', $slug);

        $tableName = $model::getTableName();
        $select = '
            SELECT
                "slug"
            FROM "'.$tableName.'"
            WHERE "'.self::SLUG_KEY_NAME.'" ~ ?
            ORDER BY "slug" ASC
        ';
        $binds = ['^[0-9]+-'.Str::limit($slug, intval(self::SLUG_MAX_LENGTH / 2))];
        $similarResult = DB::select($select, $binds);


        if (!$similarResult) {
            return Str::limit("1-$slug", self::SLUG_MAX_LENGTH, '');
        }

        $lastSlugNumModel = 0;

        foreach ($similarResult as $item) {
            $num = preg_replace('/^(\d+)-(.+)$/', '$1', $item->slug);

            if ($num > $lastSlugNumModel) {
                $lastSlugNumModel = $num;
            }
        }

        $lastSlugNum = $lastSlugNumModel + 1;

        return Str::limit("$lastSlugNum-$slug", self::SLUG_MAX_LENGTH, '');
    }
}
