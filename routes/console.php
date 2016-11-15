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
    $specification = Imperial\Simp\Specification::findOrFail($id);

    dispatch(new Imperial\Simp\Jobs\ParseSpecificationJob($specification));

})->describe('Parse a specification file.');

Artisan::command('specs:identify', function () {
    $specifications = Imperial\Simp\Specification::where('mime', 'application/pdf')->whereNull('parser')->limit(10)->get();

    foreach ($specifications as $specification) {
        try {
            $specification->getParser();
        }
        catch (Exception $e) {
            $specification->parser = 'UNKNOWN';
            $specification->save();
        }
    }

})->describe('Identify the parser for a specification file.');
