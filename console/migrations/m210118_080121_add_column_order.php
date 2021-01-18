<?php

use yii\db\Migration;

/**
 * Class m210118_080121_add_column_order
 */
class m210118_080121_add_column_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("orders_contact", "partner_name", $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210118_080121_add_column_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_080121_add_column_order cannot be reverted.\n";

        return false;
    }
    */
}
