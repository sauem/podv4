<?php

use yii\db\Migration;

/**
 * Class m201216_165524_table_order
 */
class m201216_165524_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders_contact', 'warehouse_id' , $this->integer()->null());
        $this->addColumn('orders_contact', 'transport_id' , $this->integer()->null());
        $this->addColumn('orders_contact', 'checking_number' , $this->string()->null());
        $this->addColumn('orders_contact', 'order_status' , $this->string(50)->null());
        $this->addColumn('orders_contact', 'cross_status' , $this->string(50)->null());
        $this->addColumn('orders_contact', 'vendor_status' , $this->string(50)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201216_165524_table_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201216_165524_table_order cannot be reverted.\n";

        return false;
    }
    */
}
