<?php

namespace App\Console\Commands;

use App\NewsAggregator;
use Illuminate\Console\Command;

class AggregateNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aggregate:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregates news from different sources and saves them locally in database';

    protected NewsAggregator $aggregator;

    public function __construct(NewsAggregator $aggregator)
    {
        parent::__construct();
        $this->aggregator = $aggregator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->aggregator->aggregate();
    }
}
