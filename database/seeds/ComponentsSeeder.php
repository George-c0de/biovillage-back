<?php

use App\Models\ComponentCollectionModel;
use App\Models\ComponentModel;
use App\Models\SeoModel;
use Illuminate\Database\Seeder;

class ComponentsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        ComponentModel::truncate();
        ComponentModel::insert([
            [
                'name' => '',
                'slug' => ComponentModel::MAIN_SETTINGS,
                'caption' => 'Основные настройки сайта',
                'title' => '',
                'content' => '',
                'properties' => json_encode([
                    'email' => 'email@mail.com',
                    'phone' => '88001112233',
                    'phoneHotline' => '88001112233',
                    'address' => '',
                    'mapLink' => '',
                    'operationMode' => [],
                ]),
            ],
            [
                'name' => '',
                'slug' => ComponentModel::SOCIALS,
                'caption' => 'Социальные сети',
                'title' => '',
                'content' => '',
                'properties' => null,
            ],
            [
                'name' => '',
                'slug' => ComponentModel::SLIDER_ON_INDEX,
                'caption' => 'Слайдер на главной',
                'title' => '',
                'content' => '',
                'properties' => null,
            ],
            [
                'name' => '',
                'slug' => ComponentModel::OUR_SPECIALISTS,
                'caption' => 'Список специалистов',
                'title' => 'Наши специалисты',
                'content' => '',
                'properties' => null,
            ],
        ]);

        SeoModel::truncate();
        SeoModel::insert([
            [
                'entityId' => ComponentModel::getComponentId('MAIN_SETTINGS'),
                'groupName' => ComponentModel::MAIN_SETTINGS,
                'seoTitle' => '',
                'seoDescription' => '',
                'ogLocale' => '',
                'ogType' => '',
                'ogSiteName' => '',
                'ogTitle' => '',
                'ogDescription' => '',
                'ogUrl' => '',
                'ogImage' => '',
                'ogVideo' => '',
            ],
        ]);
    }
}
