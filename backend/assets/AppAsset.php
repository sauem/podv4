<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/theme';
    public $css = [
        'libs/c3/c3.min.css',
        'https://use.fontawesome.com/releases/v5.3.1/css/all.css',
        'libs/bootstrap-select/css/bootstrap-select.min.css',
        'libs/jquery-toast-plugin/jquery.toast.min.css',
        'libs/flatpickr/flatpickr.min.css',
        'libs/multiselect/css/multi-select.css',
        'libs/bootstrap-select/css/bootstrap-select.min.css',
        'libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css',
        'libs/selectize/css/selectize.bootstrap3.css',
        'css/app.min.css',
        'css/icons.min.css',
        'css/site.css?v=2.0'
    ];
    public $js = [
        'js/vendor.js',
        'libs/bootstrap-select/js/bootstrap-select.min.js',
        'libs/jquery-toast-plugin/jquery.toast.min.js',
        'libs/flatpickr/flatpickr.min.js',
        'libs/sweetalert2/sweetalert2.all.min.js',
        'libs/apexcharts/apexcharts.min.js',
        'libs/selectize/js/standalone/selectize.min.js',
        'libs/excel/xlsx/dist/xlsx.full.min.js',
        'libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js',
        'libs/bootstrap-select/js/bootstrap-select.min.js',
        'libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js',
        'libs/multiselect/js/jquery.multi-select.js',
        'libs/jquery-mask-plugin/jquery.mask.min.js',
        'libs/autonumeric/autoNumeric-min.js',
        'libs/d3/d3.min.js',
        'libs/c3/c3.min.js',
        'js/pages/form-wizard.init.js',
        'js/app.min.js',
        ['js/search.js', 'async' => true],
        'js/main.js',
        'js/upload.js',
        ['js/ModalRemote.js', 'async' => true],
        'js/func.js?v=1.2',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
