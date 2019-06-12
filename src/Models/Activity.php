<?php

namespace Padosoft\Laravel\ActivitylogExtended\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity as ActivityBase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class Activity extends ActivityBase
{
    protected static function boot()
    {
        parent::boot();
        Activity::saving(function ($model) {
            $model->url = $model->resolveUrl();
            $model->user_agent = $model->resolveUserAgent();
            $model->ip = $model->resolveIp();
        });
    }

    public function resolveIp()
    {
        return Request::ip();
    }

    public function resolveUserAgent()
    {
        return Request::header('User-Agent');
    }

    public function resolveUrl()
    {
        if (!App::runningInConsole()) {
            return Request::fullUrlWithQuery([]);
        }
        if (in_array('schedule:run', $_SERVER['argv'])) {
            return 'scheduler';
        }

        return 'console';
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function scopeAllRelations(Builder $query, \Illuminate\Database\Eloquent\Model $subject): Builder
    {
        return $query
            ->where(function ($q) use ($subject) {
                $q->where('subject_type', '=', $subject->getMorphClass())
                  ->where('subject_id', $subject->getKey());
            })->orWhere(function ($q) use ($subject) {
                $q->where('causer_type', '=', $subject->getMorphClass())
                  ->where('causer_id', $subject->getKey());
            })->orWhere('properties', 'LIKE', '%' . $subject->getTable() . '":"' . $subject->getKey() . '"%');
    }
}
