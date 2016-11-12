<?php

namespace Imperial\Simp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Imperial\Simp\Specification;

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
        $this->specification->parse();
    }
}
