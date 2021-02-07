<?php

namespace common\helper;

use backend\models\OrdersContact;
use Illuminate\Support\Arr;
use yii\helpers\ArrayHelper;
use yii2mod\settings\models\SettingModel;
use function GuzzleHttp\Psr7\str;

class Helper
{
    static function printf($model)
    {
        echo "<pre>";
        var_dump($model);
        echo "</pre>";
        exit;
    }

    static function string($str)
    {
        return rtrim($str, ', ');
    }

    static function toFloat($number)
    {
        $number = str_replace(',', '', $number);
        return str_replace(trim(Helper::symbol()), '', trim($number));
    }

    static function formatCUR($num, $symbol = null, $decimal = 2)
    {
        if (!$symbol) {
            $symbol = Helper::symbol();
        }
        return $symbol . number_format($num, $decimal, '.', ',');
    }

    static function decimal($num, $decimal = 2)
    {
        return number_format($num, $decimal, '.', ',');
    }

    static function makeUpperString($str)
    {
        return strtoupper(static::toLower($str));
    }

    static function firstError($model)
    {
        $modelErrs = $model->getFirstErrors();
        foreach ($modelErrs as $err) {
            return $err;
        }
        return "No error founded";
    }

    static function dateFormat($time, $timestamp = false)
    {
        if (is_numeric($time)) {
            return date('d-m-Y', $time) . ($timestamp ? ' <small class="text-muted">' . date('H:i:s') . '</small>' : '');
        }
        return $time;
    }

    static function toDate($time)
    {
        if (is_numeric($time)) {
            return date('d/m/Y H:i:s', $time);
        }
        return $time;
    }

    static function countries()
    {
//        return ArrayHelper::map(\Yii::$app->params['countries'], 'code', function ($item) {
//            return $item['code'] . ' - ' . $item['name'];
//        });
        return \Yii::$app->params['countries'];
    }

    static function countryName($code)
    {
        return ArrayHelper::getValue(self::countries(), $code, '--');
    }

    static function toLower($str)
    {
        if (!is_string($str)) {
            return null;
        }
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

    static function defaultImage($name = 'product')
    {
        return ArrayHelper::getValue([
            'product' => '/theme/images/default.jpg'
        ], $name);
    }

    static function makeCodeIncrement($lastID, $country = "VN", $prefix = '#CC')
    {
        $defaultCode = $prefix . $country . "0000000";
        $maxLen = strlen($lastID);
        return substr_replace($defaultCode, $lastID, -$maxLen);
    }

    static function isEmpty($val)
    {
        if (!$val || $val === '' || $val === null || empty($val) || !isset($val)) {
            return true;
        }
        return false;
    }

    static function isNull($val)
    {
        if (!$val || $val === '' || $val === null || empty($val)) {
            return "";
        }
        return $val;
    }

    static function compareTimeNow()
    {
        $limitTimer = Helper::setting('phone_prioritize_time');
        $now = date('H:i A');
        return $limitTimer > $now;
    }

    static function timer($string, $plus = 0)
    {
        if (strtotime($string)) {
            return strtotime($string);
        }
        $time = \DateTime::createFromFormat('m-d-Y', $string)->format('m-d-Y');
        if ($plus > 0) {
            return strtotime($time . " +$plus day");
        }
        return strtotime($time);
    }

    static function setting($name)
    {
        $bk = SettingModel::findOne(['section' => "Common", "key" => $name]);
        return ArrayHelper::getValue($bk, 'value');
    }

    static function calculate($number1, $number2, $reverse = false, $float = 2)
    {
        if (!$number2 || $number2 <= 0) {
            return 0;
        }
        if ($reverse) {
            return round($number1 * $number2 / 100, $float);

        }
        return round($number1 / $number2 * 100, $float);
    }

    static function isRole($role)
    {
        return \Yii::$app->user->identity->role === $role;
    }

    static function symbol($country = null)
    {
        $symbols = ArrayHelper::getColumn(\Yii::$app->params, 'symbols', '');
        if (!$country || !$symbols) {
            $country = \Yii::$app->cache->get('country');
        }
        if (!$country) {
            return '';
        }
        return ArrayHelper::getValue($symbols, $country, '');
    }

    static function printString($model, $string = false)
    {
        $items = Helper::isEmpty($model->skuItems) ? null : $model->skuItems;
        if (!$items || empty($items)) {
            return '---';
        }
        $str = "";
        foreach ($items as $item) {
            $str .= $item->sku . "*" . $item->qty . " + ";
        }
        if ($string) {
            return substr($str, 0, -2);
        }
        return "<small>" . substr($str, 0, -2) . "</small>";
    }
}
