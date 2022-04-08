<?php

namespace Padosoft\Laravel\ActivitylogExtended\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity as ActivityBase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/**
 * Padosoft\Laravel\ActivitylogExtended\Models\Activity.
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forBatch(string $batchUuid)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity hasBatch()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity inLog($logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity query()
 */
class Activity extends ActivityBase
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        Activity::saving(function ($model) {
            $model->url = $model->resolveUrl();
            $model->user_agent = $model->resolveUserAgent();
            $model->ip = $model->resolveIp();
        });
    }

    public function resolveIp(): ?string
    {
        return Request::ip();
    }

    public function resolveUserAgent(): array|string|null
    {
        return Request::header('User-Agent');
    }

    public function resolveUrl(): string
    {
        if (!App::runningInConsole()) {
            return Request::fullUrlWithQuery([]);
        }
        if (in_array('schedule:run', $_SERVER['argv'])) {
            return 'scheduler';
        }

        return 'console';
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
