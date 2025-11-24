<?php

use App\Jobs\CheckDeadlinesJob;
use App\Jobs\CheckOverdueStages;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the CheckOverdueStages job to run hourly
Schedule::job(new CheckOverdueStages)->hourly();

// Schedule the CheckDeadlinesJob to run hourly
Schedule::job(new CheckDeadlinesJob)->hourly();
