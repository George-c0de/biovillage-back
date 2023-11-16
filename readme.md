# Описание

_Описание проекта_

### Установка зависимостей
  * ```composer install```
  * ```npm install```
  * ```composer global require friendsofphp/php-cs-fixer```

### Создание БД

  1. ```sudo -s```
  1. ```su postgres```
  1. ```psql```
  1. ```create user <USER> with password '<PASS>'```
  1. ```create database <DBNAME> encoding 'utf8' owner '<USER>' lc_collate 'C.UTF-8' lc_ctype 'C.UTF-8' template 'template0';```
  1. ```grant all privileges on database <DBNAME> to <USER> ;```
  1. ```\c <DBNAME>;```
  1. ```CREATE EXTENSION pg_trgm;```
  1. Выходим при помощи сочетания клавиш```Ctrl+D```
  1. Подключаемся к БД уже как обычный пользователь ```psql -U <USER> -W -h localhost <DBNAME>``` При подключении попросят ввести пароль. Вводим <PASS>
  1. ```CREATE SCHEMA main;```
  1. ```SET search_path TO main, public;```
  1. ```ALTER database "<DBNAME>" SET search_path TO main, public;```


Тоже самое нужно проделать и для создания БД с логами. При создании этой БД нужно будет поменять имя БД и пропустить шаг создания пользователя. В остальном аналогично.

# Конфигурирование
  * ```cp .env.example .env```

Открываем файл ```.env``` и вводим свои значения параметров подключения к БД

### Генерация ключа
  * ```./aristan key:generate```
  
### Работа с изображениями
  * ```wget https://imagemagick.org/download/binaries/magick```
  * ```chmod +x magick```

### Настройка git
  * Задайте своё имя командой ```git config --global user.name "YOUR_NAME"```
  * Задайте свой email командой ```git config --global user.email "YOUR_BITBUKET_EMAIL"```
  * Скопируйте файл ```pre-commit``` в папку ```.git/hooks``` с заменой
  
# Заполнение данными
  * ```php artisan db:seed --class=DevSeeder``` - заполнение базы одной командой для дев разработки
  ----------------------------------------------------------
  * ```php artisan db:seed --class=ClientsSeeder``` - создание клиентов
  * ```php artisan db:seed --class=SuperAdminSeeder``` - создание суперадмина
  * ```php artisan db:seed --class=ComponentsSeeder``` - заполнение основных настроек сайта
  
# Очереди
  * ```php artisan queue:work --queue=orderMails``` - очередь для отправки формы обратной связи
  * ```php artisan queue:work --queue=thumbnail``` - очередь создания миниатюр для картинок
  * ```php artisan queue:work --queue=watermark``` - очередь добавления водяного знака на картинку

# Запуск проекта
  * Для запуска бекенда ```./aristan serve --host=<your ip> --port=<your port>``` 
  * Для сборки фронтенда выполните ```npm run watch```

# Структура приложения

  * ```app``` Основной код приложения
    * ```Components``` Свои или измененные компаненты laravel 
    * ```Console``` Консольные команды
    * ```Exceptions``` Исключения возникающие в приложении
    * ```Helpers``` Классы помощники и разные утилиты общего назначения
    * ```Jobs``` Задания выполняемые в очереди. Отложенно.
    * ```Mail``` Классы почты. Используются для удобства отправки почты и для простой передачи данных в почтовый шаблон.
    * ```Models``` Содержит модели приложения и классы для поиска в базе данных. Также константы относящиеся к моделям.
    * ```Providers``` Сервис провайдеры laravel
    * ```Services``` Содержит классы реализующие бизнес-логику приложения
    * ```Http``` Классы для обработки запросов
      * ```Controllers``` Контроллеры
      * ```Requests``` Обработчики форм
      * ```Middlerware``` Промежуточные слои между запросами и его обработчиками
  * ```config``` Конфигурационный файлы приложения
  * ```routes``` Файлы со списокм маршрутов приложения
  * ```storage``` Хранилище, создающихся во время работы, файликов. Загруженный файлы.
  * ```resources``` 
    * ```views``` Шаблоны страниц
    * ```lang``` Языковые ресурсы
    * ```assets``` Картинки, стили, скрипты и тд
      * ```fronto``` Ресурсы front
      * ```backo``` Ресурсы back

## Связанные с авторизацией файлы
  * Exception\Handler.php
  * Http\Middleware\RedirectIfAuthenticated
## Особенности работы с несколькими типами юзеров
  * Защита роутов. Вместо ```middleware('auth')``` и ```middleware('guest')``` нужно писать ```middleware('auth:client')``` и ```middleware('guest:client')```. Это касается и админрв.
  * В шаблонах нужно указывать имя guard. Вместо ```@auth ... @endauth``` нужно писать ```@auth('client') ... @endauth```

# Установка пакетов

Отдельные части системы поставляются в виде пакетов composer. Для их установки нужно сделать следующее:
  * ```composer install``` - Установим пакеты composer
  * ```cd vendor && rm -rf packages``` - Удалим папку с установленными пакетами
  * ```ln -s ../packages``` - Сделаем ссылку на пакеты. Это нужно что бы при переключении веток не переустанавливать пакеты



