<?php

namespace Imperial\Simp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Imperial\Simp\Specification;
use GuzzleHttp\Client;
use Storage;

class DownloadSpecificationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $specification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();

        $request = $client->get($this->specification->url);

        $contents = $request->getBody();

        $mime = head($request->getHeader('Content-Type'));
        $etag = head($request->getHeader('ETag'));

        Storage::put($this->specification->path, $contents);

        $this->specification->retrieve($mime, $etag);
    }
}
