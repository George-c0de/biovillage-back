<?php

use Illuminate\Support\Facades\Route;

/**
 * Маршруты для АПИ.
 *
 * Полный класс контроллеров можно не указывать,
 * т.к они ищутся в `App\Http\Controllers\Api`
 */

Route::prefix('v1')->group(function() {

    # Common routes
    Route::get('/login', 'PlugController@loginRoute')->middleware(['guest:api'])->name('login');
    Route::get('/settings', 'SettingsController@clientSettings');
    Route::get('/catalog', 'CatalogController@index');
    Route::get('/catalog/promo', 'CatalogController@promo');
    Route::get('/gifts', 'GiftController@publicList');

    # Billing routes
    Route::post( '/billing/payment-listener', 'BillingController@paymentListener');


    # Admin routes
    Route::prefix('admin')->group(function() {

        #Pickpoints
        Route::apiResource('pickpoints', PickpointController::class)->middleware(['auth:sanctum', 'admin']);

        # Groups
        Route::get('/groups/{id}', 'GroupController@show')->middleware(['auth:sanctum', 'admin']);
        Route::get('/groups', 'GroupController@index')->middleware(['auth:sanctum', 'admin']);
        Route::post('/groups/{id}', 'GroupController@update')->middleware(['auth:sanctum', 'admin']);
        Route::post('/groups', 'GroupController@store')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/groups/{id}', 'GroupController@delete')->middleware(['auth:sanctum', 'admin']);

        # Units
        Route::post('/units', 'UnitController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/units', 'UnitController@index')->middleware(['auth:sanctum', 'admin']);
        Route::get('/units/{id}', 'UnitController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/units/{id}', 'UnitController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/units/{id}', 'UnitController@destroy')->middleware(['auth:sanctum', 'admin']);

        # Tags
        Route::get('/tags', 'TagController@index')->middleware(['auth:sanctum', 'admin']);
        Route::post('/tags', 'TagController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/tags/{id}', 'TagController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/tags/{id}', 'TagController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/tags/{id}', 'TagController@destroy')->middleware(['auth:sanctum', 'admin']);

        # Settings
        Route::get('/settings', 'SettingsController@index')->middleware(['auth:sanctum', 'admin']);
        Route::post('/settings', 'SettingsController@update')->middleware(['auth:sanctum', 'admin']);

        # Main slider
        Route::get('/slider', 'SliderController@index')->middleware(['auth:sanctum', 'admin']);
        Route::post('/slider', 'SliderController@store')->middleware(['auth:sanctum', 'admin']);
        Route::post('/slider/{id}', 'SliderController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/slider/{id}', 'SliderController@destroy')->middleware(['auth:sanctum', 'admin']);
        Route::get('/slider/{id}', 'SliderController@show')->middleware(['auth:sanctum', 'admin']);

        # Products
        Route::post('/products', 'ProductController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/products', 'ProductController@index')->middleware(['auth:sanctum', 'admin']);
        Route::get('/products/{id}', 'ProductController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/products/{id}', 'ProductController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/products/{id}', 'ProductController@destroy')->middleware(['auth:sanctum', 'admin']);

        # Admins
        Route::post('/login', 'AdminController@login')->middleware('guest:api');

        Route::post('/admins', 'AdminController@store')->middleware(['auth:sanctum', 'admin:superadmin']);
        Route::get('/admins', 'AdminController@list')->middleware(['auth:sanctum', 'admin']);
        Route::get('/admins/{id}', 'AdminController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/admins/{id}', 'AdminController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/admins/{id}', 'AdminController@destroy')->middleware(['auth:sanctum', 'admin:superadmin']);

        # Gifts
        Route::post('/gifts', 'GiftController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/gifts', 'GiftController@index')->middleware(['auth:sanctum', 'admin']);
        Route::get('/gifts/{id}', 'GiftController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/gifts/{id}', 'GiftController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/gifts/{id}', 'GiftController@destroy')->middleware(['auth:sanctum', 'admin']);

        # Delivery intervals
        Route::post('/di', 'DeliveryIntervalsController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/di', 'DeliveryIntervalsController@index')->middleware(['auth:sanctum', 'admin']);
        Route::get('/di/{id}', 'DeliveryIntervalsController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/di/{id}', 'DeliveryIntervalsController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/di/{id}', 'DeliveryIntervalsController@destroy')->middleware(['auth:sanctum', 'admin']);

        # Clients
        Route::get('/clients', 'ClientController@list')->middleware(['auth:sanctum', 'admin']);
        Route::get('/clients/{id}', 'ClientController@info')->middleware(['auth:sanctum', 'admin']);
        Route::post('/clients/{id}', 'ClientController@updateViaManager')->middleware(['auth:sanctum', 'admin']);
        Route::post('/clients/{id}/bonuses', 'ClientController@bonuses')->middleware(['auth:sanctum', 'admin']);


        // Delivery area
        Route::post('/da/loadKml', 'DeliveryAreaController@loadKml')->middleware(['auth:sanctum', 'admin']);
        Route::get('/da', 'DeliveryAreaController@index')->middleware(['auth:sanctum', 'admin']);
        Route::post('/da', 'DeliveryAreaController@store')->middleware(['auth:sanctum', 'admin']);
        Route::get('/da/{id}', 'DeliveryAreaController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/da/{id}', 'DeliveryAreaController@update')->middleware(['auth:sanctum', 'admin']);

        # Orders
        Route::get('/orders', 'OrderController@index')->middleware(['auth:sanctum', 'admin']);
        Route::get('/orders/{id}/items', 'OrderController@items')->middleware(['auth:sanctum', 'admin']);
        Route::get('/orders/{id}/payments', 'OrderController@payments')->middleware(['auth:sanctum', 'admin']);
        Route::get('/orders/{id}/gifts', 'OrderController@gifts')->middleware(['auth:sanctum', 'admin']);
        Route::get('/orders/{id}', 'OrderController@show')->middleware(['auth:sanctum', 'admin']);
        Route::post('/orders/{id}', 'OrderController@update')->middleware(['auth:sanctum', 'admin']);

        # Order operations
        Route::post('/orders/{id}/complete', 'OrderController@complete')->middleware(['auth:sanctum', 'admin']);
        Route::post('/orders/{id}/cancel', 'OrderController@cancel')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/orders/{id}', 'OrderController@refund')->middleware(['auth:sanctum', 'admin']);

        // Order items
        Route::get('/orders/items/{id}', 'OrderItemController@showItem')->middleware(['auth:sanctum', 'admin']);
        Route::get('/orders/gifts/{id}', 'OrderItemController@showGift')->middleware(['auth:sanctum', 'admin']);
        Route::post('/orders/items/{id}', 'OrderItemController@updateItem')->middleware(['auth:sanctum', 'admin']);
        Route::post('/orders/gifts/{id}', 'OrderItemController@updateGift')->middleware(['auth:sanctum', 'admin']);
        Route::post('/orders/items', 'OrderItemController@massUpdate')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/orders/items/{id}', 'OrderItemController@cancelItem')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/orders/gifts/{id}', 'OrderItemController@cancelGift')->middleware(['auth:sanctum', 'admin']);

        # Packer boy
        Route::post('/packer-boy/pack/{id}', 'PackerBoyController@pack')->middleware(['auth:sanctum', 'admin']);
        Route::post('/packer-boy/unpack/{id}', 'PackerBoyController@unpack')->middleware(['auth:sanctum', 'admin']);

        # Images, certificates and etc.
        Route::post('/images/{groupName}/{entityId}/upload', 'ImageController@store')->middleware(['auth:sanctum', 'admin']);
        Route::post('/images/{groupName}/{entityId}/update', 'ImageController@update')->middleware(['auth:sanctum', 'admin']);
        Route::delete('/images/{groupName}/{entityId}/destroy', 'ImageController@destroy')->middleware(['auth:sanctum', 'admin']);
        Route::get('/images/{groupName}/{entityId}', 'ImageController@show')->middleware(['auth:sanctum', 'admin']);

    });

    Route::prefix('client')->group(function() {

        // Client
        Route::get('/', 'ClientController@me')->middleware('auth:sanctum');
        Route::post('/', 'ClientController@update')->middleware('auth:sanctum');
        Route::post('/birthday', 'ClientController@updateBirthday')->middleware('auth:sanctum');
        Route::delete('/avatar', 'ClientController@deleteAvatar')->middleware('auth:sanctum');

        // Login and registration
        Route::post('/register/request', 'ClientController@registerRequest')->middleware(['guest:api', 'throttle:50']);
        Route::post('/register/verify', 'ClientController@registerVerify')->middleware(['guest:api', 'throttle:50']);

        // Addresses
        Route::get('/addresses/', 'AddressController@index')->middleware(['auth:sanctum']);
        Route::post('/addresses/', 'AddressController@store')->middleware(['auth:sanctum']);
        Route::get('/addresses/{id}', 'AddressController@show')->middleware(['auth:sanctum']);
        Route::delete('/addresses/{id}', 'AddressController@destroy')->middleware(['auth:sanctum']);

        // Orders
        Route::post('/orders', 'OrderController@store')->middleware(['auth:sanctum']);
        Route::get('/orders', 'OrderController@myOrders')->middleware(['auth:sanctum']);

    });

/*
    // Логин для админов
    Route::post('admin/login', 'Auth\AdminController@login')->middleware('guest:api');
    Route::get('admin/logout', 'Auth\AdminController@logout')->middleware(['auth:sanctum', 'admin']);
    Route::get('admin/check', 'Auth\AdminController@check')->middleware(['auth:sanctum', 'admin']);
    // Логин для клиентов
    Route::post('client/login', 'Auth\ClientController@login')->middleware('guest:api');
    Route::get('client/logout', 'Auth\ClientController@logout')->middleware(['auth:sanctum', 'client']);
    Route::get('client/check', 'Auth\ClientController@check')->middleware(['auth:sanctum', 'client']);

    // Отправка формы обратной связи
    Route::post('orderSend', 'MailController@send')
        ->middleware('guest:api', 'throttle:10');

    // Сео
    Route::get('seo/{groupName}/{entityId}', 'SeoController@show')->middleware('guest:api');
    Route::post('seo/{groupName}/{entityId}', 'SeoController@store')->middleware('guest:api');
    Route::post('seo/{groupName}/{entityId}/update', 'SeoController@update')->middleware('guest:api');
    Route::delete('seo/{groupName}/{entityId}', 'SeoController@destroy')->middleware('guest:api');

    // Компоненты
    Route::get('components', 'ComponentController@index')->middleware('guest:api');
    Route::get('components/{slug}', 'ComponentController@show')->middleware('guest:api');
    Route::post('components/{slug}', 'ComponentController@update')->middleware('guest:api');
    // Коллекции компонентов
    Route::post('components/{slug}/collection', 'ComponentCollectionController@store')->middleware('guest:api');
    Route::post('components/{slug}/collection/{id}', 'ComponentCollectionController@update')->middleware('guest:api');
    Route::delete('components/{slug}/collection/{id}', 'ComponentCollectionController@destroy')->middleware('guest:api');

    // Каталог
    Route::get('catalog', 'CatalogController@index')->middleware('guest:api');
    Route::get('catalog/{path}', 'CatalogController@show')
        ->where('path', '[a-zA-Z0-9/_-]+');
*/
});

Route::any('{any}', 'PlugController@anyRoute');
