<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMessagesTable extends AbstractMigration
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
        // 'messages' टेबल बनाने का कोड
        // यह टेबल हर एक भेजे गए मैसेज का रिकॉर्ड रखेगी
        $table = $this->table('messages');
        
        $table->addColumn('conversation_id', 'integer', ['signed' => false])
              ->addColumn('sender_id', 'integer', ['signed' => false, 'comment' => 'ID of the user who sent the message'])
              ->addColumn('receiver_id', 'integer', ['signed' => false, 'comment' => 'ID of the user who received the message'])
              ->addColumn('body', 'text', ['comment' => 'The actual message content'])
              ->addColumn('is_read', 'boolean', ['default' => false, 'comment' => 'To check if the message has been read'])
              
              // created_at और updated_at के लिए
              ->addTimestamps()
              
              // Foreign Keys
              ->addForeignKey('conversation_id', 'conversations', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('sender_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('receiver_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}