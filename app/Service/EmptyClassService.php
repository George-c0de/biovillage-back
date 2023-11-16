<?php

namespace App\Service;

/**
 * Вспомогательный пустой класс
 * отдаем при подключении в сервис провайдере через singleton
 * если сервис пакета не подключен то отдается этот класс
 * и все вызванные методы попадают в __call и срабатывают в холостую
 */
class EmptyClassService
{
    public function __call($name, $arguments){
        return;
    }
}
