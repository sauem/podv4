<?php

use yii\db\Migration;

/**
 * Class m210106_070205_add_column
 */
class m210106_070205_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("warehouse_histories", "code", $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210106_070205_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210106_070205_add_column cannot be reverted.\n";

        return false;
    }
    */
}
