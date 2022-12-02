<?php

use App\Timing;
use Illuminate\Database\Seeder;

class AdminTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $min = array("00", "30");
        for ($i = 00; $i < 24; $i++) {
            foreach ($min as $v) {
                Timing::create([
                    'time' =>  "$i:$v",
                    'is_active' => 1
                ]);
            }
        }
    }
}
