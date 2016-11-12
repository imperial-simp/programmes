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

Artisan::command('specs:retrieve', function () {

    $client = new Imperial\Simp\Clients\SpecificationClient;

    $client->run();

})->describe('Retrieve the list of programme specifications.');

Artisan::command('specs:sniff', function () {

$specification = Imperial\Simp\Specification::find(1696);
dd($specification->file, $specification->path);

    // $client = new GuzzleHttp\Client();
    //
    // $request = $client->get('https://www.imperial.ac.uk/media/imperial-college/study/programme-specifications/life-sciences/BSc-Biochem-w-Mgmt-w-YInd_Res.pdf');
    //
    // $contents = $request->getBody();
    // $mime = $request->getHeader('ETag');
    //
    // dd($mime);

});
