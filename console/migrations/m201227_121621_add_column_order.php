<?php

use yii\db\Migration;

/**
 * Class m201227_121621_add_column_order
 */
class m201227_121621_add_column_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("orders_contact", "vendor_status", "shipping_status");
        $this->addColumn("orders_contact", "time_paid_success", $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201227_121621_add_column_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201227_121621_add_column_order cannot be reverted.\n";

        return false;
    }
    */
}
