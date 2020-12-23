<?php

use yii\db\Migration;

/**
 * Class m201223_025544_add_column_partner
 */
class m201223_025544_add_column_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('products', 'partner_name', $this->string(255)->null());
        $this->addColumn('products', 'marketer_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201223_025544_add_column_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201223_025544_add_column_partner cannot be reverted.\n";

        return false;
    }
    */
}
