<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Padosoft\Laravel\ActivitylogExtended\Models\Activity;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\CustomDatabaseConnectionOnActivityModel;

class CustomDatabaseConnectionActivityModelTest extends TestCase
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
    public function it_uses_the_database_connection_from_the_configuration()
    {
        $model = new Activity();

        $this->assertEquals(config('activitylog.database_connection'), $model->getConnectionName());
    }

    /** @test */
    public function it_uses_a_custom_database_connection()
    {
        $model = new Activity();

        $model->setConnection('custom_sqlite');

        $this->assertNotEquals($model->getConnectionName(), config('activitylog.database_connection'));
        $this->assertEquals('custom_sqlite', $model->getConnectionName());
    }

    /** @test */
    public function it_uses_the_default_database_connection_when_the_one_from_configuration_is_null()
    {
        app()['config']->set('activitylog.database_connection', null);

        $model = new Activity();

        $this->assertInstanceOf('Illuminate\Database\SQLiteConnection', $model->getConnection());
    }

    /** @test */
    public function it_uses_the_database_connection_from_model()
    {
        $model = new CustomDatabaseConnectionOnActivityModel();

        $this->assertNotEquals($model->getConnectionName(), config('activitylog.database_connection'));
        $this->assertEquals('custom_connection_name', $model->getConnectionName());
    }
}
