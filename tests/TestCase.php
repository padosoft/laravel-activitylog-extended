<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Illuminate\Support\Arr;
use Padosoft\Laravel\ActivitylogExtended\Models\Activity;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\Article;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function checkRequirements()
    {
        //parent::checkRequirements();

        collect($this->getAnnotations())->filter(function ($location) {
            return in_array('!Travis', Arr::get($location, 'requires', []));
        })->each(function ($location) {
            getenv('TRAVIS') && $this->markTestSkipped('Travis will not run this test.');
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            ActivitylogServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory() . '/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    protected function setUpDatabase()
    {
        $this->resetDatabase();

        $this->createActivityLogTable();

        $this->createTables('articles', 'users');
        $this->seedModels(User::class, Article::class);
    }

    protected function resetDatabase()
    {
        file_put_contents($this->getTempDirectory() . '/database.sqlite', null);
    }

    protected function createActivityLogTable()
    {
        include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/create_activity_log_table.php.stub';

        (new \CreateActivityLogTable())->up();
        if (file_exists(__DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_event_column_to_activity_log_table.php.stub')) {
            include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_event_column_to_activity_log_table.php.stub';
            (new \AddEventColumnToActivityLogTable())->up();
        }
        if (file_exists(__DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_batch_uuid_column_to_activity_log_table.php.stub')) {
            include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_batch_uuid_column_to_activity_log_table.php.stub';
            (new \AddBatchUuidColumnToActivityLogTable())->up();
        }


        include_once __DIR__ . '/../migrations/enhance_activity_log_table.php.stub';

        (new \EnhanceActivityLogTable())->up();
    }

    public function getTempDirectory(): string
    {
        return __DIR__ . '/temp';
    }

    protected function createTables(...$tableNames)
    {
        collect($tableNames)->each(function (string $tableName) {
            $this->app['db']->connection()->getSchemaBuilder()->create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('text')->nullable();
                $table->timestamps();
                $table->softDeletes();

                if ($tableName === 'articles') {
                    $table->integer('user_id')->unsigned()->nullable();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->text('json')->nullable();
                    $table->string('interval')->nullable();
                    $table->decimal('price')->nullable();
                }
            });
        });
    }

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(1, 0) as $index) {
                if ($modelClass == Article::class) {
                    $causer = User::first();
                    $modelClass::create(['name' => "name {$index}", 'user_id' => $causer->id]);
                } else {
                    $modelClass::create(['name' => "name {$index}"]);
                }
            }
        });
    }

    /**
     * @return \Spatie\Activitylog\Models\Activity|null
     */
    public function getLastActivity()
    {
        return Activity::all()->last();
    }

    public function doNotMarkAsRisky()
    {
        $this->assertTrue(true);
    }

    public function isLaravel6OrLower(): bool
    {
        $majorVersion = (int)substr(App::version(), 0, 1);

        return $majorVersion <= 6;
    }

    public function loginWithFakeUser()
    {
        $user = new $this->user();

        $user::find(1);

        $this->be($user);

        return $user;
    }
}
