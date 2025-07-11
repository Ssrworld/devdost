<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateReviewsTable extends AbstractMigration
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
        // 'reviews' टेबल बनाने का कोड
        $table = $this->table('reviews');
        
        $table->addColumn('project_id', 'integer', ['signed' => false])
              ->addColumn('reviewer_id', 'integer', ['signed' => false, 'comment' => 'ID of the user giving the review'])
              ->addColumn('reviewee_id', 'integer', ['signed' => false, 'comment' => 'ID of the user being reviewed'])
              ->addColumn('rating', 'integer', ['limit' => 1, 'comment' => 'Rating from 1 to 5'])
              ->addColumn('comment', 'text', ['null' => true])
              ->addColumn('review_type', 'string', ['limit' => 20, 'comment' => 'e.g., client_to_developer, developer_to_client'])
              
              // created_at और updated_at के लिए
              ->addTimestamps()
              
              // Foreign Keys
              ->addForeignKey('project_id', 'projects', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('reviewer_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('reviewee_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              
              // टेबल को डेटाबेस में बनाना
              ->create();
    }
}