<?php


namespace common\models;


use yii\base\Model;

class Common extends Model
{
    public $sheet_id;

    public function rules()
    {
        return [
            [['sheet_id',], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'sheet_id' => 'ID google sheet backup contact',
        ];
    }
}