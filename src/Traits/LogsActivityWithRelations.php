<?php
/**
 * Copyright (c) Padosoft.com 2018.
 */

/**
 * Copyright (c) https://laracasts.com/@phildawson
 */

namespace Padosoft\Laravel\ActivitylogExtended\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\ActivitylogServiceProvider;

trait LogsActivityWithRelations
{
    use RelationshipsTrait;
    use LogsActivity {
        LogsActivity::attributeValuesToBeLogged as attributeValuesToBeLoggedBase;
    }

    /*protected static function bootLogsActivityWithRelations(){
        self::bootLogsActivity();
    }*/

    /**
     * @param string $processingEvent
     * @return array
     */
    public function attributeValuesToBeLogged(string $processingEvent): array
    {
        $properties = $this->attributeValuesToBeLoggedBase($processingEvent);

        $properties = $this->setRelationsToBeLogged($properties);

        return $properties;
    }


    /**
     * @return mixed
     */
    public function getAllRelatedActivites()
    {
        $model = ActivitylogServiceProvider::getActivityModelInstance();
        $subject = $this;
        return $model->where(function ($q) use ($subject) {
            $q->where('subject_type', '=', $subject->getMorphClass())
                ->where('subject_id', $subject->getKey());
        })->orWhere(function ($q) use ($subject) {
            $q->where('causer_type', '=', $subject->getMorphClass())
                ->where('causer_id', $subject->getKey());
        })->orWhere('properties', 'LIKE', '%' . $subject->getTable() . '":"' . $subject->getKey() . '"%')->get();
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    public function setRelationsToBeLogged(array $properties): array
    {
        $relationships = $this->getModelRelations();

        foreach ($relationships as $key => $relationship) {
            if (($relationship['type'] == 'BelongsTo' || $relationship['type'] == 'MorphTo') && $relationship['foreignKey'] !== '') {
                $key = $relationship['foreignKey'] . '.' . $relationship['foreignTable'];
                $foreignKey = $relationship['foreignKey'];
                $properties['relations'][$key] = (string)$this->$foreignKey;
                if (isset($properties['old'][$foreignKey])) {
                    $key = $relationship['foreignKey'] . '_old.' . $relationship['foreignTable'];
                    $properties['relations'][$key] = (string)$properties['old'][$foreignKey];
                }
            }
        }

        return $properties;
    }
}
