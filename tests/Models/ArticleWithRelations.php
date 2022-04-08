<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Padosoft\Laravel\ActivitylogExtended\Traits\LogsActivityWithRelations;
use Spatie\Activitylog\LogOptions;

class ArticleWithRelations extends Model
{
    use LogsActivityWithRelations;

    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at'];
    protected static $logOnlyDirty = true;

    protected $table = 'articles';

    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
