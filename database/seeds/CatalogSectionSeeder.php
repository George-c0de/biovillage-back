<?php

use Carbon\Carbon;
use App\Models\CatalogSection;
use Illuminate\Database\Seeder;

class CatalogSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            $data[$i] = [
                'name' => 'Category ' . ($i + 1),
                'createdAt' => Carbon::now(),
                'updatedAt' => Carbon::now(),
                'deletedAt' => null,
            ];
        }

        CatalogSection::insert($data);
    }
}
