<?php
use App\Template;
use Illuminate\Database\Seeder;
use App\Resource;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('products')->delete();

        (new \App\Product())->refresh();
    }

}
