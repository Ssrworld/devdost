<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateConversationsTable extends AbstractMigration
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
        // 'conversations' टेबल बनाने का कोड
        // यह टेबल सिर्फ यह रिकॉर्ड रखेगी कि किन दो यूज़र्स के बीच बातचीत हो रही है
        $table = $this->table('conversations');
        
        $table->addColumn('user1_id', 'integer', ['signed' => false, 'comment' => 'ID of the first user in the conversation'])
              ->addColumn('user2_id', 'integer', ['signed' => false, 'comment' => 'ID of the second user in the conversation'])
              
              // created_at और updated_at के लिए
              ->addTimestamps()
              
              // Foreign Keys
              ->addForeignKey('user1_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('user2_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}