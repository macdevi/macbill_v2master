<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('billing:generate-daily')
    ->dailyAt('00:05')
    ->withoutOverlapping();
