<?php

use yii\db\Migration;

/**
 * Class m201213_162426_table_ware_house
 */
class m201213_162426_table_ware_house extends Migration
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
        $this->createTable('{{%warehouse}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'country' => $this->string(50),
            'status' => $this->string(25)->null(),
            'note' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%warehouse_transaction}}', [
            'id' => $this->primaryKey(),
            'warehouse_id' => $this->integer(),
            'product_sku' => $this->string(255),
            'qty' => $this->integer()->defaultValue(1),
            'transaction_type' => $this->string(50),
            'total_average' => $this->double(15.2),
            'po_code' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'wearehosue_fk_transaction',
            'warehouse_transaction',
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201213_162426_table_ware_house cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201213_162426_table_ware_house cannot be reverted.\n";

        return false;
    }
    */
}
