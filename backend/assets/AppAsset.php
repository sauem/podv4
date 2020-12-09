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
        'libs/flatpickr/flatpickr.min.css',
        'libs/selectize/css/selectize.bootstrap3.css',
        'css/bootstrap.min.css',
        'https://use.fontawesome.com/releases/v5.3.1/css/all.css',
        'css/app.min.css',
        'css/icons.min.css',
        'css/site.css'
    ];
    public $js = [
        'js/vendor.js',
        'libs/flatpickr/flatpickr.min.js',
        'https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js',
        'js/HandlebarsHelper.js',
        'libs/apexcharts/apexcharts.min.js',
        'libs/selectize/js/standalone/selectize.min.js',
        'js/app.min.js',
        'js/upload.js',
        ['js/ModalRemote.js', 'async' => true],
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
