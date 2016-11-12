<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('specs:retrieve {source?}', function ($source = null) {

    if ($source) {
        $clients = Imperial\Simp\Source::whereIn('name', explode(',', $source))->get();
    }
    else {
        $clients = Imperial\Simp\Source::get();
    }

    foreach ($clients as $client) {
        $client->run();
    }

})->describe('Retrieve the list of specifications from online sources.');

Artisan::command('specs:parse {id}', function ($id) {
    $spec = Imperial\Simp\Specification::findOrFail($id);
    $parser = $spec->getParser();
    dd(get_class($parser));

})->describe('Parse a specification file.');
