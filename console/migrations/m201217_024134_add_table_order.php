<?php

use yii\db\Migration;

/**
 * Class m201217_024134_add_table_order
 */
class m201217_024134_add_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders_contact', 'po_code', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201217_024134_add_table_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_024134_add_table_order cannot be reverted.\n";

        return false;
    }
    */
}
