<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Настройка путей для фронта
 */

Route::get('robots.txt', 'RootController@robots');
Route::get('sitemap.xml', 'RootController@sitemap');

Route::post('subscribe', 'SubscribeController@index')->middleware('throttle:30');
Route::get('agreement.txt', 'RootController@privacyPolicy');
Route::get('privacy-policy', 'RootController@privacyPolicy');
Route::view('/billing/success', 'front.billing.success');


Route::get('{any?}', 'RootController@index');
