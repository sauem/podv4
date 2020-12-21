<?php

use yii\db\Migration;

/**
 * Class m201219_051909_add_column_order
 */
class m201219_051909_add_column_order extends Migration
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
        $this->createTable('{{%orders_refund}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(255),
            'time_refund_success' => $this->string(255),
            'checking_number' => $this->string(50),
            'sku' => $this->string(255),
            'qty' => $this->integer()->defaultValue(0),
            'po_code' => $this->string(50)->null(),
            'total' => $this->double(15.2)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->addColumn('orders_contact', 'cod_cost', $this->double(15.2)->defaultValue(0));
        $this->addColumn('orders_contact', 'time_shipped_success', $this->integer()->null());
        $this->addColumn('orders_contact', 'collection_fee', $this->double(15.2)->defaultValue(0));
        $this->addColumn('orders_contact', 'transport_fee', $this->double(15.2)->defaultValue(0));
        $this->addColumn('orders_contact', 'remittance_date', $this->integer()->null());
        $this->addColumn('orders_contact', 'cross_check_code', $this->string(255)->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201219_051909_add_column_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201219_051909_add_column_order cannot be reverted.\n";

        return false;
    }
    */
}
