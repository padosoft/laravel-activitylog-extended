<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Padosoft\Laravel\ActivitylogExtended\Traits\LogsActivityWithRelations;

class ArticleWithRelations extends Model
{
    use LogsActivityWithRelations;

    protected static $logAttributes           = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at'];
    protected static $logOnlyDirty            = true;

    protected $table = 'articles';

    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
