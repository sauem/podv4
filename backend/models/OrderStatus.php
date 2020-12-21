<?php


namespace backend\models;

/**
 * Class OrderStatus
 * @package backend\models
 */
class OrderStatus extends OrdersContact
{
    public $warehouse_id;
    public $transport_id;
    public $sub_transport_id;
    public $ids;

    public function rules()
    {
        return [
            [['warehouse_id', 'transport_id'], 'required'],
            [['warehouse_id', 'transport_id', 'ids', 'sub_transport_id'], 'safe']
        ];
    }

}