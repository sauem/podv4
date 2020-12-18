<?php

use yii\db\Migration;

/**
 * Class m201217_030402_change_column
 */
class m201217_030402_change_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('orders_contact', 'order_status', 'payment_status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201217_030402_change_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_030402_change_column cannot be reverted.\n";

        return false;
    }
    */
}
