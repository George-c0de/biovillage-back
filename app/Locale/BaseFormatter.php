<?php

namespace App\Locale;
use Carbon\Carbon;
use DateTime;

/**
 * Переводим разные величины(время, деньги, расстояние) в представления,
 * которые приняты в каждой стране.
 *
 * Базовый формат настроен для РФ
 */
class BaseFormatter
{
    public $dbDateTimeFormat = 'Y-m-d H:i:s.u';
    public $shortDbDateTimeFormat = 'Y-m-d H:i:s';
    public $dbDateFormat = 'Y-m-d';
    public $dbDateTimeISOFormat = 'Y-m-d\TH:i:s.u';
    public $dbDateTimeISOFormatZ = 'Y-m-d\TH:i:s.u\Z';

    // Формат даты и времени
    public $dateFormat = 'd.m.Y';
    public $timeFormat = 'H:i';
    public $dateTimeFormat = 'd.m.Y H:i';
    public $fullDateTimeFormat = 'd.m.Y H:i:s';

    /**
     * Переводим дату и время из форматы БД в объект DateTime
     */
    public function dbDtToDt($val) {
        $dt = DateTime::createFromFormat($this->dbDateTimeFormat, $val);
        if(empty($dt)) {
            $dt = DateTime::createFromFormat($this->dbDateTimeISOFormat, $val);
        }
        if(empty($dt)) {
            $dt = DateTime::createFromFormat($this->shortDbDateTimeFormat, $val);
        }
        if(empty($dt)) {
            $dt = DateTime::createFromFormat($this->dbDateFormat, $val);
        }
        if(empty($dt)) {
            $dt = DateTime::createFromFormat($this->dbDateTimeISOFormatZ, $val);
        }
        return $dt;
    }

    /**
     * Переводим дату и время из формата БД в формат, принятый в РФ
     * @param string $val
     * @return string
     */
    public function dbDtToDtStr($val) {

        $dt = self::dbDtToDt($val);
        if($dt === false) {
            return '';
        }
        return $dt->format($this->dateTimeFormat);
    }

    /**
     * Переводим дату из формата БД в формат, принятый в РФ
     * @param string $val
     * @return string
     */
    public function dbDateToDateStr($val) {

        $dt = self::dbDtToDt($val);
        if($dt === false) {
            return null;
        }
        return $dt->format($this->dateFormat);
    }

    /**
     * Transform date to database date format
     * @param $val
     * @return null|string
     */
    public function dateToDbStr($val) {
        if(!$val instanceof DateTime) {
            $dt = DateTime::createFromFormat($this->dateTimeFormat, $val);
            if(empty($dt)) {
                $dt = DateTime::createFromFormat($this->dateFormat, $val);
            }
            if(empty($dt)) {
                return null;
            }
        } else {
            $dt = $val;
        }

        return $dt->format($this->dbDateFormat);
    }

    /**
     * Transform date time to database date format
     * @param $val
     * @return null|string
     */
    public function dateTimeToDbStr($val) {
        if(!$val instanceof DateTime) {
            $dt = DateTime::createFromFormat($this->dateTimeFormat, $val);
            if(empty($dt)) {
                $dt = DateTime::createFromFormat($this->dateFormat, $val);
            }
            if(empty($dt)) {
                return null;
            }
        } else {
            $dt = $val;
        }

        return $dt->format($this->dbDateTimeFormat);
    }


}