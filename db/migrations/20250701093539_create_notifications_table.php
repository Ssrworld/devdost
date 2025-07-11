<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateNotificationsTable extends AbstractMigration
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
        // 'notifications' टेबल बनाने का कोड
        $table = $this->table('notifications');
        
        $table->addColumn('user_id', 'integer', ['signed' => false, 'comment' => 'The user who will receive the notification'])
              ->addColumn('message', 'string', ['limit' => 255])
              ->addColumn('link', 'string', ['limit' => 255, 'null' => true, 'comment' => 'Link to the relevant page, e.g., project details'])
              ->addColumn('is_read', 'boolean', ['default' => false, 'comment' => 'To check if the notification has been read'])
              
              // created_at और updated_at के लिए
              ->addTimestamps()
              
              // Foreign Key
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}