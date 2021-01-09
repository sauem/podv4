<?php


namespace common\models;


use yii\base\Model;

class Common extends Model
{
    public $sheet_id;
    public $phone_prioritize_time;
    public $callback_to_cancel_time;

    public function rules()
    {
        return [
            [['phone_prioritize_time', 'callback_to_cancel_time'], 'number'],
            [['sheet_id',], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'sheet_id' => 'ID google sheet backup contact',
            'phone_prioritize_time' => 'Thời gian ưu tiên số mới (ngày)',
            'callback_to_cancel_time' => 'Thời gian hủy số gọi lại (ngày)',
        ];
    }
}