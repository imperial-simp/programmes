<?php

use Illuminate\Database\Seeder;

abstract class AbstractJsonTableSeeder extends Seeder
{
    public function data($file)
    {
        return json_decode(Storage::disk('seeds')->get($file.'.json'), true);
    }
}
