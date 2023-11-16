<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseFrontController;

class RootController extends BaseFrontController {
    /**
     * Показываем основную страницу админки
     */
    public function index() {
        return view('front.index');
    }

    /**
     * Показываем основную страницу админки
     * Шаблоны лежат /resources/errors/*.blade.php можно их там переделать
     * @param $code
     */
    public function error($code) {
        abort($code);
    }

    /**
     * Файл роботс
     */
    public function robots() {
        return response()
            ->view('front.robots')
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Файл сайтмапа
     */
    public function sitemap() {
        return response()
            ->view('front.sitemap', [
                'var' => '',
            ])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Privacy Policy
     * @return \Illuminate\Http\Response
     */
    public function privacyPolicy() {
        return response()->view('front.agreement');
    }
}
