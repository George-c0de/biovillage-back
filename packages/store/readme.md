# Install

### Install package
  * Add the following "repositories" key below the "scripts" section in composer.json file of your Laravel app
  * ```
    "repositories": [
        {
            "type": "path",
            "url": "packages/store",
            "symlink": true
        }
    ],
    ```
  * ```composer require packages/store```
  * ```php artisan vendor:publish --provider="Packages\Store\SeoServiceProvider" --tag="config"```
  
  * Remove package
  * ```composer remove packages/store```
  
  
# Описание работы пакета store (склад)

## Описание базы

Для работы склада используются 5 таблицы

* stores - склады
* storePlaces - места складов
* storeOperations - операция по складу или складам
* storeOperationFiles - файлы операции
* storeOperationContents - содержание операции

### stores - склады

Поля таблицы:

* id
* name (str) - имя склада
* order (int) - порядок сортировки
* systemPlaceId (int) - id системного место склада
* type (str) - тип склада (для продуктов - 'product', для подарков - 'gift')
* createdAt (dt) - время создания
* updatedAt (dt) - время обновления
* deletedAt (dt) - время удаления

##### Процесс создания склада

Склад может быть двух типов:
* product - для подуктов
* gift - для подарков

После создания склада любого из типов автоматически создаются 2 места для этого склада одно 
под **системное место** (Id системного места записывается в поле systemPlaceId в таблице stores) 
другое дефолтное место.

Таблица stores имеет связь с storePlaces один ко многим тоесть
у одного склада может быть много мест

### storePlaces - места складов

Поля таблицы:

* id
* name (str) - имя места
* storeId (int) - id склада
* order (int) - порядок сортировки
* isSystem (bool) - галочка является ли данное место системным, по дефолту false
* createdAt (dt) - время создания
* updatedAt (dt) - время обновления
* deletedAt (dt) - время удаления

Место должно принадлежать складу и связывается сним через поле storeId.

Если место обозначено как isSystem = true это системное место - оно создается автоматически при создании склада и нужно для обработки частного случая:
***списание или заказ товара записи о котором нету на складе***

### storeOperations - операция по складу или складам

Поля таблицы:

* id
* type (str) - тип операции
* status (str) - статус операции
* adminId (int) - id админа который совершил операцию
* clientId (int) - id клиента который совершил операцию
* orderId (int) - id заказа по которому осуществилась операция
* comment (text) - комментарий
* storeIds (array) - массив id складов по которым произошла операция
* isMultipleStores (bool) - если операция была по одному складу - false, если по многим то - true
* createdAt (dt) - время создания
* updatedAt (dt) - время обновления

Типы операций:

* put - поступление на склад
* take - списание со склада
* correction - коррекция
* resetCorrection - сброс перед коррекцией
* ordering - заказ с сайта (технически это тоже самое списание)

Статусы операций:

* completed - завершено
* reserve - резерв
* cancel - отмена

Нюансы типов и статусов:

Такие типы операций как put, take, correction, resetCorrection выполняются админом поэтому им они сразу записываются со статусом - completed

Тип операции ordering предполагает заказ продукта клиентом через сайт для этого типа используются все статусы. При заказе клиентом товаров 
записи в таблицу не происходит, она происходит после того как админ укомплектует заказ по адресу /admin/pack_orders/{id} и укажет фактическое 
колличество по каждой позиции заказанного товара эта информация далее попадет в таблицу storeOperationContents, запись на этапе комплектации
происходит со статусом reserve, далее если клиент оплатит покупку статус перейдет в completed, если нет то cancel

Создать операцию может или админ или клиент сделав заказ для понимания кто инициализировал оперцию у нас есть 3 поля adminId, clientId, orderId
* Если операцию совершил админ то заполнятся поле adminId а поля clientId и orderId остаются равными null
* Если операцию совершил клиент то заполняются сразу 2 поля clientId и orderId а поле adminId остается равныи null

storeIds и isMultipleStores. 

Операции доступные админу (put, take, correction, resetCorrection) работают в разрезе одного склада следовательно в
массив storeIds попадает только один склад а isMultipleStores = false. 
 
Если поисходит операция ordering тоесть заказ товара клиентом, то эта операция может затронуть множество складов. Принцип работы такой:
когда клиент заказывает товар мы ищем его по всем складам и списывам там где он есть пока не наберем нужное колличество если же нужного
колличества не оказалось мы списываем его с системного места, по параметрам последнего поступления или же по дефолтным параметрам 
(об этом подробнее в разделе storeOperationContents)

### storeOperationFiles - файлы операции

Поля таблицы:

* storeOperationId (int) - id операции
* name (str) - имя файла
* src (str) - путь к файлу

К операции могут прикрепить несколько файлов, чеки и т.д

### storeOperationContents - содержание операции

Поля таблицы:

* storeOperationId (int) - id операции
* storePlaceId (int) - id места на складе
* productId (int) - id продукта
* unitId (int) - id ед. измерения продукта
* giftId (int) - id подарка
* realUnits (int) - изменненное количество 
* storeId (int) - id склада
* netCostPerStep (int) - цена за шаг ед. измерения измеряется в копейках если это продукт (в бонусах если это подарок)
* netCost (int) - общая цена за все товары в колонке realUnits измеряется в копейках если это продукт (в бонусах если это подарок)

Запись содержиния операции относится к операции, у одной операции может быть множетво содержаний.

Если операция производится по складу с типом product (продуктовый склад) то заполняются поля productId и unitId а giftId будет равен Null, 
unitId дублирутется с таблицы products т.к в последствии unit для товара может поменятся. В данном случае realUnits будет означать
общее колличество в базовых единицах измерения деленное на шаг единицы измерения отдельный для каждого продукта, например зелень базовая единица
измерения граммы у нас 600 гр. продается по 300 гр. (шаг единицы измерения) realUnits = 600 / 300 = 2

netCost цена за все колличество в элементе операции измеряется если это продукт измеряется в копейках если подарок то в бонусах

netCostPerStep если это продукт то это цена за шаг единицы измения продукта в копейках, если это подарок то это бонусы за один подарок

Если операция производится по складу с типом gift (склад подарков) то заполняется поле giftId а поля productId, unitId, netCostPerStep и netCost
бдут равны null. realUnits в данном случае будет означать колличество подарков в штуках т.е реальное колличество

## Описание api

### Stores

##### GET '/stores' - список складов

request:
* type - тип склада (product, gift)

response:

```
{
    "result": {
        "stores": [
            {
                "id": 1,
                "name": "Store product",
                "order": 1,
                "createdAt": "06.08.2021 08:47",
                "updatedAt": "06.08.2021 08:47",
                "deletedAt": null,
                "systemPlaceId": 1,
                "type": "product"
            },
            ...
        ]
    },
    "success": true
}
```

##### POST '/stores/{id}' - информация по складу

id - id склада

request:

response:

```
{
    "result": {
        "id": 1,
        "name": "Store product",
        "order": 1,
        "createdAt": "06.08.2021 08:47",
        "updatedAt": "06.08.2021 08:47",
        "deletedAt": null,
        "systemPlaceId": 1,
        "type": "product"
    },
    "success": true
}
```

##### POST '/stores' - создание склада

request:
* id - id склада
* name - имя склада
* type - тип склада (product, gift)
* order - порядок сортировки

response:

```
    {
        "result": {
            "id": 1,
            "name": "Store product",
            "order": 1,
            "createdAt": "2021-08-06T10:57:51.000000Z",
            "updatedAt": "2021-08-06T10:57:51.000000Z",
            "deletedAt": null,
            "systemPlaceId": 1,
            "type": "product"
        },
        "success": true
    }
```

##### POST '/stores/{id}' - обновление информации о складе

id - id склада

request:
* name - имя склада
* type - тип склада (product, gift)
* order - порядок сортировки

response:

```
    {
        "result": {
            "id": 1,
            "name": "Store product",
            "order": 1,
            "createdAt": "2021-08-06T10:57:51.000000Z",
            "updatedAt": "2021-08-06T10:57:51.000000Z",
            "deletedAt": null,
            "systemPlaceId": 1,
            "type": "product"
        },
        "success": true
    }
```

##### DELETE '/stores/{id}' - удаление склада

id - id склада

request:

response:

```
{
    "result": {
        "message": "OK"
    },
    "success": true
}
```

##### GET '/stores/{id}/contents' - содержание склада

id - id склада

request:
* perPage - сколько элементов на странице
* page - старница
* productId - id продукта
* giftId - id подарка
* orderId - id заказа
* createdAtBegin - дата создания от
* createdAtEnd - дата создания до
* updatedAtBegin - дата обновления от
* updatedAtEnd - дата обновления до

response:

```
{
    "result": {
        "contents": [
            {
                "giftId": null,
                "giftName": null,
                "productId": 1,
                "productName": "Бананан 8eeflud8shg",
                "storePlaceId": 2,
                "storePlaceName": "Полка #1",
                "unitId": 1,
                "unitShortName": "Ед zlzl12q87sc",
                "unitStep": 10,
                "unitShortDerName": "Ед производная",
                "unitFactor": 1000,
                "realUnits": 1
            }
        ],
        "pager": {
            "currentPage": 1,
            "lastPage": 1,
            "perPage": 25,
            "total": 1,
            "hasMorePages": false
        }
    },
    "success": true
}
```

### Store Places

##### GET '/store-places' - список всех местов

request:

response:

```
{
    "result": {
        "storePlaces": [
            {
                "id": 1,
                "storeId": 1,
                "name": "кредит",
                "order": 0,
                "createdAt": "06.08.2021 08:47",
                "updatedAt": "06.08.2021 08:47",
                "deletedAt": null,
                "isSystem": true
            },
            ...
        ]
    },
    "success": true
}
```

##### GET '/store-places/{id}' - список местов для склада

id - id склада

request:

response:

```
{
    "result": {
        "storePlaces": [
            {
                "id": 1,
                "storeId": 1,
                "name": "кредит",
                "order": 0,
                "createdAt": "06.08.2021 08:47",
                "updatedAt": "06.08.2021 08:47",
                "deletedAt": null,
                "isSystem": true
            },
            ...
        ]
    },
    "success": true
}
```

##### POST '/store-places' - создание места для склада

request:
* storeId - id склада
* name - имя места для склада
* order - порядок сортировки

response:

```
{
    "result": {
        "id": 7,
        "storeId": 1,
        "name": "new",
        "order": 1,
        "createdAt": "06.08.2021 11:23",
        "updatedAt": "06.08.2021 11:23",
        "deletedAt": null,
        "isSystem": false
    },
    "success": true
}
```

##### POST '/store-places/{id}' - изменение информации о месте на складе

id - id места склада

request:
* storeId - id склада
* name - имя места для склада
* order - порядок сортировки

response:

```
{
    "result": {
        "id": 7,
        "storeId": 1,
        "name": "new",
        "order": 1,
        "createdAt": "06.08.2021 11:23",
        "updatedAt": "06.08.2021 11:23",
        "deletedAt": null,
        "isSystem": false
    },
    "success": true
}
```


##### DELETE '/store-places/{id}' - удаление места на складе

id - id места склада

request:

response:

```
{
    "result": {
        "message": "OK"
    },
    "success": true
}
```

### Store Operations

##### GET '/store-operations' - список операций

request:
* storeId
* perPage - сколько элементов на странице
* page - старница
* adminId - id админа
* clientId - id клиента
* orderId - id заказа
* type - тип операции
* createdAtBegin - дата создания от
* createdAtEnd - дата создания до
* updatedAtBegin - дата обновления от
* updatedAtEnd - дата обновления до
* sort - поле сортировки
* sortDirect - порядок сортировки

response:

```
{
    "result": {
        "operations": [
            {
                "id": 1,
                "type": "put",
                "status": "completed",
                "adminId": 1,
                "clientId": null,
                "orderId": null,
                "comment": null,
                "createdAt": "2021-08-06 14:13:06",
                "updatedAt": "2021-08-06 14:13:06",
                "storeIds": "{1}",
                "isMultipleStores": false,
                "isFiles": false
            },
            ...
        ],
        "pager": {
            "currentPage": 1,
            "lastPage": 1,
            "perPage": 25,
            "total": 1,
            "hasMorePages": false
        }
    },
    "success": true
}
```

##### GET '/store-operations/{id}' - просмотр операции

id - id операции

request:
* storeId - id склада
* perPage - сколько элементов на странице
* page - старница
* adminId - id админа
* clientId - id клиента
* orderId - id заказа
* type - тип операции
* createdAtBegin - дата создания от
* createdAtEnd - дата создания до
* updatedAtBegin - дата обновления от
* updatedAtEnd - дата обновления до
* sort - поле сортировки
* sortDirect - порядок сортировки

response:

```
{
    "result": {
        "adminId": 1,
        "adminName": "Superadmin",
        "clientId": null,
        "clientName": null,
        "id": 1,
        "type": "put",
        "status": "completed",
        "orderId": null,
        "comment": null,
        "createdAt": "2021-08-06 14:13:06",
        "updatedAt": "2021-08-06 14:13:06",
        "storeIds": [
            1
        ],
        "isMultipleStores": false,
        "contents": [
            {
                "giftId": null,
                "giftName": null,
                "productId": 1,
                "productName": "Бананан 8eeflud8shg",
                "price": 550,
                "groupName": "Бананs 0xu17goddyv",
                "groupBgColor": "#D3D3D3",
                "storeName": "Store product",
                "storePlaceName": "Полка #1",
                "storeOperationId": 1,
                "storePlaceId": 2,
                "realUnits": 1,
                "unitId": 1,
                "storeId": 1,
                "netCostPerStep": 50000,
                "netCost": 50,
                "unitShortName": "Ед zlzl12q87sc",
                "unitShortDerName": "Ед производная",
                "unitStep": 10,
                "unitFactor": 1000
            }
        ]
    },
    "success": true
}
```

##### GET '/store-operation/put' - операция поступления продуктов на склад

request:
* storeId - id склада
* contents.*.productId - id продукта
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* contents.*.netCost - общая стоимость всего поступления товара
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "id": 2,
        "type": "put",
        "status": "completed",
        "adminId": 1,
        "clientId": null,
        "orderId": null,
        "comment": null,
        "createdAt": "2021-08-09T14:38:25.000000Z",
        "updatedAt": "2021-08-09T14:38:25.000000Z",
        "storeIds": "{1}",
        "isMultipleStores": false,
        "files": [
            {
                "name": "file name 1",
                "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                "storeOperationId": 2
            }
        ]
    },
    "success": true
}
```

##### GET '/store-operation/take' - операция списания продуктов со склада

request:
* storeId - id склада
* force - (boolean) галочка уходить в минус принудительно или нет
* contents.*.productId - id продукта
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "operation": {
            "id": 3,
            "type": "take",
            "status": "completed",
            "adminId": 1,
            "clientId": null,
            "orderId": null,
            "comment": null,
            "createdAt": "2021-08-09T14:46:17.000000Z",
            "updatedAt": "2021-08-09T14:46:17.000000Z",
            "storeIds": "{1}",
            "isMultipleStores": false,
            "files": [
                {
                    "name": "file name 1",
                    "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                    "storeOperationId": 2
                }
            ]
        },
        "errors": [
            {
                "productId": 1,
                "storePlaceId": 2,
                "shortage": -12
            }
        ]
    },
    "success": true
}
```

##### GET '/store-operation/correction' - операция коррекции продуктов на складе

request:
* storeId - id склада
* contents.*.productId - id продукта
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* contents.*.netCost - общая стоимость всего поступления товара
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "id": 5,
        "type": "correction",
        "status": "completed",
        "adminId": 1,
        "clientId": null,
        "orderId": null,
        "comment": null,
        "createdAt": "2021-08-09T14:47:58.000000Z",
        "updatedAt": "2021-08-09T14:47:58.000000Z",
        "storeIds": "{1}",
        "isMultipleStores": false,
        "files": [
            {
                "name": "file name 1",
                "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                "storeOperationId": 2
            }
        ]
    },
    "success": true
}
```

##### GET '/store-operations/product/{id}' - последние 5 операций для продукта

id - id продукта

request:

response:

```
{
    "result": [
        {
            "createdAt": "2021-08-09 17:34:24",
            "updatedAt": "2021-08-09 17:34:24",
            "netCostPerStep": 23000,
            "unitId": 1,
            "unitShortName": "Ед f7wl3oo445j",
            "unitShortDerName": "Ед производная",
            "unitStep": 10,
            "unitFactor": 1000
        },
        {
            "createdAt": "2021-08-09 17:38:25",
            "updatedAt": "2021-08-09 17:38:25",
            "netCostPerStep": 23000,
            "unitId": 1,
            "unitShortName": "Ед f7wl3oo445j",
            "unitShortDerName": "Ед производная",
            "unitStep": 10,
            "unitFactor": 1000
        }
    ],
    "success": true
}
```

##### GET '/store-operations/product-residue/{id}' - запрос остатка продукта

id - id продукта

request:

response:

```
{
    "result": [
        {
            "productId": 1,
            "storeId": 1,
            "storePlaceId": 1,
            "unitId": 1,
            "realUnits": 0,
            "storeName": "Store product",
            "storePlaceName": "кредит",
            "unitShortName": "Ед f7wl3oo445j",
            "unitShortDerName": "Ед производная",
            "unitStep": 10,
            "unitFactor": 1000
        },
        {
            "productId": 1,
            "storeId": 1,
            "storePlaceId": 2,
            "unitId": 1,
            "realUnits": 2,
            "storeName": "Store product",
            "storePlaceName": "Полка #1",
            "unitShortName": "Ед f7wl3oo445j",
            "unitShortDerName": "Ед производная",
            "unitStep": 10,
            "unitFactor": 1000
        }
    ],
    "success": true
}
```

##### GET '/store-operation/clear-system-place' - обнуление сервисного места на складе (в сервисное место поступают все непонятные минусы)

id - id продукта

request:
* storeId - id склада

response:

```
{
    "result": {
        "message": "OK"
    },
    "success": true
}
```























##### GET '/store-gift-operation/put' - операция поступления продуктов на склад

request:
* storeId - id склада
* contents.*.giftId - id подарка
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* contents.*.netCost - общая стоимость всего поступления товара
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "id": 2,
        "type": "put",
        "status": "completed",
        "adminId": 1,
        "clientId": null,
        "orderId": null,
        "comment": null,
        "createdAt": "2021-08-09T14:38:25.000000Z",
        "updatedAt": "2021-08-09T14:38:25.000000Z",
        "storeIds": "{1}",
        "isMultipleStores": false,
        "files": [
            {
                "name": "file name 1",
                "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                "storeOperationId": 2
            }
        ]
    },
    "success": true
}
```

##### GET '/store-gift-operation/take' - операция списания продуктов со склада

request:
* storeId - id склада
* force - (boolean) галочка уходить в минус принудительно или нет
* contents.*.giftId - id подарка
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "operation": {
            "id": 3,
            "type": "take",
            "status": "completed",
            "adminId": 1,
            "clientId": null,
            "orderId": null,
            "comment": null,
            "createdAt": "2021-08-09T14:46:17.000000Z",
            "updatedAt": "2021-08-09T14:46:17.000000Z",
            "storeIds": "{1}",
            "isMultipleStores": false,
            "files": [
                {
                    "name": "file name 1",
                    "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                    "storeOperationId": 2
                }
            ]
        },
        "errors": [
            {
                "giftId": 1,
                "storePlaceId": 2,
                "shortage": -12
            }
        ]
    },
    "success": true
}
```

##### GET '/store-gift-operation/correction' - операция коррекции продуктов на складе

request:
* storeId - id склада
* contents.*.giftId - id продукта
* contents.*.realUnits - количество в базовых единицах
* contents.*.storePlaceId - id места склада
* contents.*.netCost - общая стоимость всего поступления товара
* files.*.name - имя загружаемого файла
* files.*.src - файл

response:

```
{
    "result": {
        "id": 5,
        "type": "correction",
        "status": "completed",
        "adminId": 1,
        "clientId": null,
        "orderId": null,
        "comment": null,
        "createdAt": "2021-08-09T14:47:58.000000Z",
        "updatedAt": "2021-08-09T14:47:58.000000Z",
        "storeIds": "{1}",
        "isMultipleStores": false,
        "files": [
            {
                "name": "file name 1",
                "src": "/storage/store/b2/b2e45c06b49aa2196a6aba67af86e8a7.md",
                "storeOperationId": 2
            }
        ]
    },
    "success": true
}
```

##### GET '/store-gift-operations/gift-residue/{id}' - запрос остатка продукта

id - id подарка

request:

response:

```
{
    "result": [
        {
            "giftId": 1,
            "storeId": 2,
            "storePlaceId": 4,
            "realUnits": 1,
            "storeName": "Store gift",
            "storePlaceName": "Полка #1"
        }
    ],
    "success": true
}
```

##### GET '/store-gift-operation/clear-system-place' - обнуление сервисного места на складе (в сервисное место поступают все непонятные минусы)

id - id подарка

request:
* storeId - id склада

response:

```
{
    "result": {
        "message": "OK"
    },
    "success": true
}
```
