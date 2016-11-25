<?php

namespace Imperial\Simp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Imperial\Simp\Specification;
use Storage;

class ParseSpecificationJob implements ShouldQueue
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
        $parser = $this->specification->parse();

        $contents = json_encode($this->specification->contents, JSON_PRETTY_PRINT);

        Storage::disk('specs')->put('json/'.$this->specification->file.'.json', $contents);

        Storage::disk('specs')->put('txt/'.$this->specification->file.'.txt', $parser->getRawText());

        if (count($parser->getErrors())) {
            echo 'The following warnings were encountered when attempting to parse the file'.PHP_EOL;
            echo '  "'.$this->specification->file.'":'.PHP_EOL;
            echo 'with parser '.get_class($parser).PHP_EOL;
            var_dump($parser->getErrors());
        }
        else {
            echo 'Successfully parsed the file'.PHP_EOL;
            echo '  "'.$this->specification->file.'"'.PHP_EOL;
            echo 'with parser '.get_class($parser).PHP_EOL;
        }
    }
}
