<?php

use yii\db\Migration;

/**
 * Class m201224_145641_add_column
 */
class m201224_145641_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("contacts", "category", $this->string(255)->null());
        $this->addColumn("products", "marketer_rage_start", $this->integer()->null());
        $this->addColumn("products", "marketer_rage_end", $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201224_145641_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201224_145641_add_column cannot be reverted.\n";

        return false;
    }
    */
}
