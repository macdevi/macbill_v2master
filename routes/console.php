<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('billing:generate')->monthlyOn(1, '00:05');
Schedule::command('billing:isolate-overdue')->dailyAt('00:10');