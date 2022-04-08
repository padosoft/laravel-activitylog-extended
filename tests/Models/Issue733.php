<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Padosoft\Laravel\ActivitylogExtended\Traits\LogsActivityWithRelations;
use Spatie\Activitylog\LogOptions;

class Issue733 extends Article
{
    use LogsActivityWithRelations;

    protected static $recordEvents = [
        'retrieved',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnly(['name']);
    }
}
