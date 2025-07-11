<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAcceptedBidToProjectsTable extends AbstractMigration
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
        // 'projects' टेबल को सेलेक्ट करें ताकि हम उसमें बदलाव कर सकें
        $table = $this->table('projects');

        // नए कॉलम्स को जोड़ें
        $table->addColumn('developer_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'user_id', 'comment' => 'The ID of the user (developer) who won the bid'])
              ->addColumn('accepted_bid_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'developer_id', 'comment' => 'The ID of the accepted bid'])
              
              // नए फॉरेन की (Foreign Key) कंस्ट्रेंट जोड़ें
              ->addForeignKey('developer_id', 'users', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
              ->addForeignKey('accepted_bid_id', 'bids', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
              
              // टेबल में बदलावों को सेव करें
              ->update();
    }
}