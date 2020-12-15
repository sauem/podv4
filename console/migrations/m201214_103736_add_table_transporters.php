<?php

use yii\db\Migration;

/**
 * Class m201214_103736_add_table_transporters
 */
class m201214_103736_add_table_transporters extends Migration
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
        $this->createTable('{{%transporters}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'phone' => $this->string(25)->null(),
            'address' => $this->string(50)->null(),
            'fax' => $this->string(50)->null(),
            'website' => $this->text()->null(),
            'facebook' => $this->text()->null(),
            'note' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201214_103736_add_table_transporters cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_103736_add_table_transporters cannot be reverted.\n";

        return false;
    }
    */
}
