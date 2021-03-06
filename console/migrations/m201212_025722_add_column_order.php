<?php

use yii\db\Migration;

/**
 * Class m201212_025722_add_column_order
 */
class m201212_025722_add_column_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders_contact','country', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201212_025722_add_column_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201212_025722_add_column_order cannot be reverted.\n";

        return false;
    }
    */
}
