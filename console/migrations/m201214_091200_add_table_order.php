<?php

use yii\db\Migration;

/**
 * Class m201214_091200_add_table_order
 */
class m201214_091200_add_table_order extends Migration
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
        echo "m201214_091200_add_table_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_091200_add_table_order cannot be reverted.\n";

        return false;
    }
    */
}
