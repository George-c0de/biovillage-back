<?php

namespace App\Searchers;

use ReflectionClass;

class BaseSearcher {

    /**
     * Загружаем значения в атрибуты.
     *
     * @param array $values hash
     */
    public function load(array $values) {
        $class = new ReflectionClass($this);
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $propName = $property->getName();
                if (array_key_exists($propName, $values)) {
                    $this->$propName = $values[ $propName ];
                }
            }
        }
    }

    /**
     * Очищаем все атрибуты объекта.
     */
    public function clear() {
        $class = new ReflectionClass($this);
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $propName = $property->getName();
                $this->$propName = null;
            }
        }
    }
}