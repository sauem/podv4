<?php

namespace common\helper;

use kartik\money\MaskMoney;
use pendalf89\filemanager\widgets\FileInput;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

class Component
{
    static function btnView($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='fe-eye'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-toggle' => 'tooltip',
            'title' => $text ? $text : 'Chi tiết',
            'class' => 'text-info mx-1'
        ]));
    }

    static function btnDelete($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='fe-trash'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-request-method' => 'POST',
            'data-confirm-ok' => 'Xoá',
            'data-confirm-cancel' => 'Hủy',
            'data-confirm-title' => 'Xóa đối tượng',
            'data-confirm-message' => 'Bạn có muốn xóa đối tượng này?',
            'role' => 'modal-remote',
            'class' => 'text-danger mx-1'
        ]));
    }

    static function btnUpdate($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='fe-edit'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-toggle' => 'tooltip',
            'title' => $text ? $text : 'Cập nhật',
            'class' => 'text-warning mx-1'
        ]));
    }

    static function inputMoney($model, $form, $name, $prefix = 'đ', $label = null)
    {
        return $form->field($model, $name)->widget(MaskMoney::className(), [
            'options' => [
                'placeholder' => 'Nhập số tiền...'
            ],
            'pluginOptions' => [
                'prefix' => ' ',
                'allowNegative' => true,
                'allowZero' => false,
                'precision' => 0,
                'allowEmpty' => false
            ]
        ]);
    }

    static function filePicker($model,$form, $name){
        return $form->field($model,$name)->widget(FileInput::className(),[
            'buttonTag' => 'button',
            'buttonName' => 'Browse',
            'buttonOptions' => ['class' => 'btn btn-default'],
            'options' => ['class' => 'form-control'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'thumb' => 'original',
            'imageContainer' => '.img',
            'pasteData' => FileInput::DATA_URL,
            'callbackBeforeInsert' => 'function(e, data) {
                console.log( data );
            }',
        ]);
    }
}