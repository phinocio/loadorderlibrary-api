<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::command('delete:orphaned')->daily()->onSuccess(function () {
    Log::info('✅ Delete Orphaned Files');
})->onFailure(function () {
    Log::error('❌ Delete Orphaned Files');
});

Schedule::command('delete:expired')->everyMinute()->onFailure(function () {
    Log::error('❌ Delete Expired Lists');
});
