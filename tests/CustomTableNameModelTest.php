<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Padosoft\Laravel\ActivitylogExtended\Models\Activity;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\CustomTableNameOnActivityModel;

class CustomTableNameModelTest extends TestCase
{
    public function setUp(): void
    {
        $this->activityDescription = 'My activity';
        parent::setUp();

        collect(range(1, 5))->each(function (int $index) {
            $logName = "log{$index}";
            activity($logName)->log('hello everybody');
        });
    }

    /** @test */
    public function uses_the_table_name_from_the_configuration()
    {
        $model = new Activity();

        $this->assertEquals(config('activitylog.table_name'), $model->getTable());
    }

    /** @test */
    public function uses_a_custom_table_name()
    {
        $model = new Activity();
        $newTableName = 'my_personal_activities';

        $model->setTable($newTableName);

        $this->assertNotEquals($model->getTable(), config('activitylog.table_name'));
        $this->assertEquals($newTableName, $model->getTable());
    }

    /** @test */
    public function uses_the_table_name_from_the_model()
    {
        $model = new CustomTableNameOnActivityModel();

        $this->assertNotEquals($model->getTable(), config('activitylog.table_name'));
        $this->assertEquals('custom_table_name', $model->getTable());
    }
}
