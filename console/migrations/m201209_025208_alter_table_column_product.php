<?php

use yii\db\Migration;

/**
 * Class m201209_025208_alter_table_column_product
 */
class m201209_025208_alter_table_column_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('products','name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201209_025208_alter_table_column_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201209_025208_alter_table_column_product cannot be reverted.\n";

        return false;
    }
    */
}
