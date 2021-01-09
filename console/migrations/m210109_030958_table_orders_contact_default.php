<?php

use yii\db\Migration;

/**
 * Class m210109_030958_table_orders_contact_default
 */
class m210109_030958_table_orders_contact_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210109_030958_table_orders_contact_default cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210109_030958_table_orders_contact_default cannot be reverted.\n";

        return false;
    }
    */
}
