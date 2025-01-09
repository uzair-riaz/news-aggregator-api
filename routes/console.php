<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\AggregateNews;

Schedule::command(AggregateNews::class)->everyThirtyMinutes();
