<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Настройка путей для админки
 */

Route::get('{any?}', 'RootController@index');