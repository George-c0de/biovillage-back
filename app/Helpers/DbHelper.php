<?php namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DbHelper
{
    const MAX_INT = 2147483646;

    public static function currTs(string $intervalMod = '') {
        $sql = 'CURRENT_TIMESTAMP';
        if(!empty($intervalMod)) {
            $sql .= " + INTERVAL '" . $intervalMod . "'";
        }
        return DB::raw($sql);
    }

    public static function arrayToPgArray(array $arr = []) {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);

        return str_replace('[', '{', str_replace(']', '}', str_replace('"', '', $arr)));
    }

    public static function arrayToPgPath(array $arr = []) {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        return str_replace('[', '(', str_replace(']', ')', str_replace('"', '', $arr)));
    }

    public static function pgArrayToArray($s, $start = 0, &$end = null, $bo = '{', $bc = '}') {
        if (empty($s) || $bo != $s[0]) return null;
        $return = [];
        $string = false;
        $quote = '';
        $len = strlen($s);
        $v = '';
        for ($i = $start + 1; $i < $len; ++$i) {
            $ch = $s[$i];
            if (!$string && $bc == $ch) {
                if ('' !== $v || !empty($return)) {
                    $return[] = $v;
                }
                $end = $i;
                break;
            } elseif (!$string && $bo == $ch) {
                $v = self::pgArrayToArray($s, $i, $i, $bo, $bc);
            } elseif (!$string && ',' == $ch) {
                $return[] = $v;
                $v = '';
            } elseif (!$string && ('"' == $ch || "'" == $ch)) {
                $string = true;
                $quote = $ch;
            } elseif ($string && $ch == $quote && '\\' == $s[$i - 1]) {
                $v = substr($v, 0, -1).$ch;
            } elseif ($string && $ch == $quote && '\\' != $s[$i - 1]) {
                $string = false;
            } else {
                $v .= $ch;
            }
        }
        foreach ($return as &$r) {
            if (is_numeric($r)) {
                if (ctype_digit($r)) $r = (int) $r;
                else $r = (float) $r;
            }
        }

        return $return;
    }
}
