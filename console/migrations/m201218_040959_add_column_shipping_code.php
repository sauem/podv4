<?php

use yii\db\Migration;

/**
 * Class m201218_040959_add_column_shipping_code
 */
class m201218_040959_add_column_shipping_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transporters', 'transporter_parent', $this->integer()->defaultValue(0));
        $this->addColumn('orders_contact', 'sub_transport_id', $this->integer()->null());
        $this->addColumn('orders_contact', 'sub_transport_tracking', $this->string(255)->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201218_040959_add_column_shipping_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201218_040959_add_column_shipping_code cannot be reverted.\n";

        return false;
    }
    */
}
