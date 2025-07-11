<?php

use Phinx\Seed\AbstractSeed;
use Illuminate\Support\Str; // Laravel का उपयोगी Str हेल्पर इस्तेमाल करने के लिए

class CategoriesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        // कैटेगरी का डेटा एक ऐरे में परिभाषित करें
        $categoriesData = [
            'PHP Scripts',
            'WordPress Plugins',
            'WordPress Themes',
            'JavaScript Utilities',
            'HTML & CSS Templates',
            'Mobile Apps',
        ];

        $data = [];
        foreach ($categoriesData as $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name) // 'PHP Scripts' को 'php-scripts' में बदल देता है
            ];
        }
        
        // --- यह महत्वपूर्ण हिस्सा है ---
        // पहले टेबल को खाली करें ताकि डुप्लीकेट एंट्री न बने
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->table('categories')->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        // अब, 'categories' टेबल को चुनें
        $categoriesTable = $this->table('categories');
        
        // डेटा डालें
        $categoriesTable->insert($data)->saveData();
    }
}