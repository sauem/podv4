<?php

use yii\db\Migration;

/**
 * Class m201226_045653_add_column_partner
 */
class m201226_045653_add_column_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%orders_topup}}', [
            'id' => $this->primaryKey(),
            'time' => $this->integer()->notNull(),
            'cash_source' => $this->string(255)->notNull(),
            'partner_id' => $this->integer(),
            'total' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addColumn("user", "service_fee", $this->double()->defaultValue(0));


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201226_045653_add_column_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201226_045653_add_column_partner cannot be reverted.\n";

        return false;
    }
    */
}
