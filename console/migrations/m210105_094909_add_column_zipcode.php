<?php

use yii\db\Migration;

/**
 * Class m210105_094909_add_column_zipcode
 */
class m210105_094909_add_column_zipcode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("zipcode_country", "symbol", $this->string(50)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_094909_add_column_zipcode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_094909_add_column_zipcode cannot be reverted.\n";

        return false;
    }
    */
}
