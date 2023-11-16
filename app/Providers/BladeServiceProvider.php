<?php

namespace App\Providers;

use App\Helpers\RequestHelper;
use Illuminate\Support\ServiceProvider;
use App\Components\Paginator;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Шаблон страничек пагинации
        Paginator::defaultView(
            RequestHelper::isAdmin()
                ? 'back.pagination.default'
                : 'front.pagination.default'
        );

        // Настройки форматирования
        $locale = locale();

        // Формат полей даты времени БД
        Blade::directive('datetime', function ($expression) use($locale) {

            return ''
                .'<?php '
                ."\$__dtStr = DateTime::createFromFormat('" . $locale->dbDateTimeFormat . "', $expression);"
                .'if($__dtStr) {'
                ."echo(\$__dtStr->format('".$locale->dateTimeFormat."'));"
                .'} else {'
                ."\$__dtStr = DateTime::createFromFormat('" . $locale->shortDbDateTimeFormat . "', $expression);"
                .'if($__dtStr) {'
                ."echo(\$__dtStr->format('".$locale->dateTimeFormat."'));"
                .'} else {'
                .'echo("");'
                .'}'
                .'}'
                .'?>';
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
