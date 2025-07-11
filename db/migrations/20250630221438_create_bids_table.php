<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBidsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // 'bids' टेबल बनाने का कोड
        $table = $this->table('bids');
    
        $table->addColumn('project_id', 'integer', ['signed' => false])
              ->addColumn('developer_id', 'integer', ['signed' => false]) // किस डेवलपर ने बिड लगाई
              ->addColumn('bid_amount', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('delivery_time', 'integer', ['comment' => 'Estimated time in days']) // दिनों में
              ->addColumn('status', 'string', ['limit' => 20, 'default' => 'pending', 'comment' => 'pending, accepted, rejected'])
              
              ->addTimestamps() // created_at और updated_at के लिए
              
              // Foreign Keys
              ->addForeignKey('project_id', 'projects', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('developer_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              ->create();
    }
}