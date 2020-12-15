<?php

use yii\db\Migration;

/**
 * Class m201210_104046_add_column_contact
 */
class m201210_104046_add_column_contact extends Migration
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
        echo "m201210_104046_add_column_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201210_104046_add_column_contact cannot be reverted.\n";

        return false;
    }
    */
}
