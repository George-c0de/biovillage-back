<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\ComponentCollectionModel;
use Illuminate\Support\Str;

/**
 * Модель для роботы с компонентами
 * Для небольших компонентов на сайте реализован днанный механизм
 * примеры компонентов: контакты, слайдер, отзывы и т.д
 * У компонентов могут быть коллекции с отношением один ко многим (но не обязательно)
 */
class ComponentModel extends BaseModel {
    public $timestamps = false;
    protected $connection = 'main';
    protected $table = 'components';

    protected $fillable = [
        'name',
        'caption',
        'title',
        'content',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    const MAIN_SETTINGS = 'mainSettings';
    const SOCIALS = 'socials';
    const SLIDER_ON_INDEX = 'sliderOnIndex';
    const OUR_SPECIALISTS = 'ourSpecialists';

    // Настройки отображения seo
    const IS_SEO = [
        self::MAIN_SETTINGS => true,
        self::SOCIALS => false,
        self::SLIDER_ON_INDEX => false,
        self::OUR_SPECIALISTS => false,
    ];

    // Настройки подключения коллекций
    const IS_COLLECTIONS = [
        self::MAIN_SETTINGS => false,
        self::SOCIALS => true,
        self::SLIDER_ON_INDEX => true,
        self::OUR_SPECIALISTS => true,
    ];

    public function toArray() {
        $array = parent::toArray();

        // Если в настройках для компонента is_seo == true то дабавляем массив к моделе
        $isSeo = self::IS_SEO[$this->slug];
        if ($isSeo) {
            $array['seo'] = $this->seo;
        }

        // Если в настройках не подключены коллекции удаляем их
        $isCollections = self::IS_COLLECTIONS[$this->slug];
        if (!$isCollections) {
            unset($array['collections']);
        }

        return $array;
    }

    /**
     * Отдаем id модели ComponentModel по константе
     * @param $constant
     * @return integer
     */
    public static function getComponentId($constant) {
        $slug = constant(self::class.'::'.$constant);
        $component = ComponentModel::where('slug', $slug)->first();
        return $component->id;
    }

    /**
     * Добавляем в массив компонента данные по seo
     * @return array
     */
    public function getSeoAttribute() {
        $seo = SeoModel::where([
            ['groupName', $this->slug],
            ['entityId', $this->id],
        ])->first();

        return $seo->toArray();
    }

    public function collections() {
        return $this->hasMany(ComponentCollectionModel::class, 'componentId', 'id');
    }
}
