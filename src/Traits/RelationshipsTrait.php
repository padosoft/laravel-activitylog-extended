<?php
/**
 * Copyright (c) Padosoft
 * based on https://laracasts.com/@phildawson
 */

namespace Padosoft\Laravel\ActivitylogExtended\Traits;

use ErrorException;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

trait RelationshipsTrait
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getModelRelations(): array
    {

        $model = new static;

        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {


            if ($method->class == 'Illuminate\Database\Eloquent\Model' ||
                $method->class == 'Jenssegers\Mongodb\Eloquent\Model' ||
                !empty($method->getParameters()) ||
                $method->isAbstract() ||
                $method->isConstructor() ||
                $method->isStatic() ||
                $method->getName() == 'relationships' ||
                $method->getName() == __FUNCTION__) {
                continue;
            }

            try {

                $return = $method->invoke($model);

                if ($return instanceof Relation) {

                    $type = (new ReflectionClass($return))->getShortName();
                    $model_relation = (new ReflectionClass($return->getRelated()))->getName();
                    $foreignKey = method_exists($return, 'getForeignKeyName') ? $return->getForeignKeyName() : '';
                    $foreignTable = explode('.',
                        method_exists($return, 'getQualifiedOwnerKeyName') ? $return->getQualifiedOwnerKeyName() : '');

                    if ($type == 'MorphTo') {
                        $model_relation = $this->{$return->getMorphType()};
                        if($model_relation !== null) {
                            $foreignTable[0] = $return->createModelByType($model_relation)->getTable();
                        }
                    }
                    if($model_relation !== null) {
                        $relationships[$method->getName()] = [
                            'foreignKey' => $foreignKey,
                            'foreignTable' => $foreignTable[0],
                            'type' => (new ReflectionClass($return))->getShortName(),
                            'model' => $model_relation,
                        ];
                    }
                }
            } catch (ErrorException $e) {
            } finally {

            }
        }

        return $relationships;
    }
}
