<?php

use yii\db\Migration;

/**
 * Class m201214_022117_table_warehouse_storage
 */
class m201214_022117_table_warehouse_storage extends Migration
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
        $this->createTable('{{%warehouse_histories}}', [
            'id' => $this->primaryKey(),
            'warehouse_id' => $this->integer(),
            'product_sku' => $this->string(255),
            'transaction_type' => $this->string(50),
            'order_code' => $this->integer()->null(),
            'po_code' => $this->string(255)->null(),
            'qty' => $this->integer()->defaultValue(0),
            'total_average' => $this->double(15.2)->defaultValue(0),
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
        echo "m201214_022117_table_warehouse_storage cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_022117_table_warehouse_storage cannot be reverted.\n";

        return false;
    }
    */
}
