<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductsTable extends AbstractMigration
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
        // 'products' नाम की एक नई टेबल बनाएं
        $table = $this->table('products');
        
        // कॉलम्स को परिभाषित करें
        $table->addColumn('user_id', 'integer', [
                'signed' => false, 
                'comment' => 'The ID of the user who is selling the product'
              ])
              ->addColumn('name', 'string', [
                'limit' => 255, 
                'comment' => 'Name of the software/product'
              ])
              ->addColumn('description', 'text', [
                'comment' => 'Detailed description of the product'
              ])
              ->addColumn('price', 'decimal', [
                'precision' => 10, 
                'scale' => 2, 
                'comment' => 'Price of the product in INR'
              ])
              ->addColumn('file_path', 'string', [
                'limit' => 255, 
                'comment' => 'Secure path to the downloadable zip file'
              ])
              ->addColumn('preview_image', 'string', [
                'limit' => 255, 
                'null' => true, 
                'comment' => 'Path to the product screenshot or preview image'
              ])
              // Eloquent Timestamps (created_at, updated_at)
              ->addTimestamps()
              
              // Foreign Key जो user_id को users टेबल के id से जोड़ता है
              ->addForeignKey('user_id', 'users', 'id', [
                'delete'=> 'CASCADE', // अगर यूजर डिलीट हो, तो उसके प्रोडक्ट्स भी डिलीट हो जाएं
                'update'=> 'NO_ACTION'
              ])

              // अंत में, टेबल को बनाएं
              ->create();
    }
}