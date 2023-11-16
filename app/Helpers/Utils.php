<?php namespace App\Helpers;

use App\Exceptions\ProjectException;
use App\Models\OrderModel;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Utils
 *
 * @package App\Helpers
 */
class Utils
{
    /**
     * @param $value
     *
     * @return array
     */
    private static function encodeJson($value)
    {
        if ($value instanceof Arrayable) {
            return static::encodeArrayable($value);
        } else {
            if (is_array($value)) {
                return static::encodeArray($value);
            } else {
                if (is_object($value)) {
                    return static::encodeArray((array) $value);
                } else {
                    return $value;
                }
            }
        }
    }

    /**
     * Encode an arrayable
     *
     * @param $arrayable
     *
     * @return array
     */
    private static function encodeArrayable($arrayable)
    {
        $array = $arrayable->toArray();

        return static::encodeJson($array);
    }

    /**
     * Encode an array
     *
     * @param array $array
     *
     * @return array
     */
    private static function encodeArray($array) {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[Str::camel($key)] = static::encodeJson($val);
        }

        return $newArray;
    }

    /**
     * Преобразуем значения массива null в пустые строки.
     *
     * @param array $array
     * @param mixed $val
     *
     * @return array
     */
    public static function arrayValuesEmpty(array $array, $val = '') {
        foreach ($array as $key => $value) {
            if (is_null($value)) {
                $array[$key] = $val;
            }
        }

        return $array;
    }

    /**
     * Преобразуем ключи массива в camelCase.
     *
     * @param array $array
     *
     * @return array
     */
    public static function camelArr($array)
    {
        return static::encodeArray($array);
    }

    /**
     * Преобразуем ключи массива в snake_case.
     *
     * @param array $array
     *
     * @return array
     */
    public static function snakeArr($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $keyNew = Str::snake($key);
            $newArray[$keyNew] = $array[$key];
        }

        return $newArray;
    }

    /**
     * Преобразуем результат запроса в camelCase.
     *
     * @param $rows
     *
     * @return array
     */
    public static function camelRows($rows)
    {
        return array_map(
            function($val) {
                return Utils::camelArr((array)$val);
            },
            $rows ?? []
        );
    }

    /**
     * @param string $message
     * @param array $data
     *
     * @throws Exception
     */
    public static function raise($message = '', $data = [])
    {
        throw new ProjectException(
            sprintf("%s\n%s", $message, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    /**
     * Check in array empty keys
     * @param $array
     * @param $keys
     */
    public static function raiseIfEmpty($array, $keys) {
        if(!is_array($keys)) {
            $keys = [ $keys ];
        }
        foreach($keys as $k) {
            if(empty($array[$k])) {
                static::raise('Empty key ' . $k);
            }
        }
    }

    /**
     * Trim для многобайтовых строк.
     *
     * @param string $str
     *
     * @return null|string|string[]
     */
    public static function mbTrim($str)
    {
        return preg_replace("/(^\s+)|(\s+$)/us", "", $str);
    }

    /**
     * Регистронезависимое сравнение многбайтовых строк.
     *
     * @param string $str1
     * @param string $str2
     * @param null $encoding
     *
     * @return int
     */
    public static function mbStrCmp($str1, $str2, $encoding = null)
    {
        if (null === $encoding) {
            $encoding = mb_internal_encoding();
        }

        return strcmp(mb_strtoupper($str1, $encoding), mb_strtoupper($str2, $encoding));
    }

    /**
     * Сравнение 2х строк.
     *
     * @param string $str1
     * @param string $str2
     *
     * @return bool
     */
    public static function strCompare($str1, $str2) : bool
    {
        return static::mbStrCmp($str1, $str2) == 0;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function camelCaseKeys($array)
    {
        $out = [];
        foreach ($array as $key => $value) {
            $out[Str::snake($key)] = is_array($value) ? static::camelCaseKeys($value) : $value;
        }

        return $out;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function mbUcFirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function replaceRusToEng($string)
    {
        $converter = [
            'а' => 'a', 'А' => 'A',
            'б' => 'b', 'Б' => 'B',
            'в' => 'b', 'В' => 'B',
            'е' => 'e', 'Е' => 'E',
            'ё' => 'e', 'Ё' => 'E',
            'к' => 'k', 'К' => 'K',
            'м' => 'm', 'М' => 'M',
            'н' => 'h', 'Н' => 'H',
            'о' => 'o', 'О' => 'O',
            'р' => 'p', 'Р' => 'P',
            'с' => 'c', 'С' => 'C',
            'т' => 't', 'Т' => 'T',
            'у' => 'y', 'У' => 'Y',
            'х' => 'x', 'Х' => 'X',
        ];

        return strtr($string, $converter);
    }

    /**
     * Преобразуем объект в массив.
     *
     * @param $obj
     * @param array $arr
     *
     * @return array
     */
    public static function objectToArray($obj, &$arr = [])
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }

        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $arr[$key] = array();
                self::objectToArray($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }

    /**
     * Get current Timestamp.
     *
     * @param string $format
     *
     * @return string
     */
    public static function currentDateTime($format = 'Y-m-d H:i:s')
    {
        return Carbon::now()->format($format);
    }

    /**
     * Add # to color
     * @param $color
     * @return string
     */
    public static function hexColor($color) {
        return '#' . strval($color);
    }

    /**
     * Gen full url
     * @param $url
     * @return string
     */
    public static function fullUrl($url, $noStorage = false) {
        if(empty($url)) {
            return '';
        }

        $url = ltrim($url, '/storage');
        $url = ltrim($url, '/');
        if($noStorage) {
            return env('APP_URL') . '/' . $url;
        };
        return env('APP_URL') . Storage::url( $url);
    }

    /**
     * Convert null values to empty string in array
     * @param $fields
     * @param $array
     */
    public static function nullToStrInArray($fields, &$array) {
        if(!is_array($fields)) {
            $fields = [$fields];
        }
        foreach($fields as $f) {
            if(array_key_exists($f, $array)) {
                $array[$f] = strval($array[$f]);
            }
        }
    }

    /**
     * Create array from argument ever if argument is not array
     * @param $arr - array of any
     * @return array
     */
    public static function safeArray($arr) {
        if(!is_array($arr)) {
            return [$arr];
        }
        return $arr;
    }


}
