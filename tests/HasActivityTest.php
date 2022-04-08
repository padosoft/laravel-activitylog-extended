<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Padosoft\Laravel\ActivitylogExtended\Models\Activity;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Padosoft\Laravel\ActivitylogExtended\Traits\LogsActivityWithRelations;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class HasActivityTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new class() extends User {
            use LogsActivity;
            use CausesActivity;
            use SoftDeletes;

            public function getActivitylogOptions(): LogOptions
            {
                return LogOptions::defaults();
            }
        };

        $this->assertCount(0, Activity::all());
    }

    /** @test */
    public function it_can_log_activity_on_subject_by_same_causer()
    {
        //$this->app['config']->set('activitylog.activity_model', \Padosoft\Laravel\ActivitylogExtended\Models\Activity::class);
        $user = $this->loginWithFakeUser();

        $user->name = 'HasActivity Name';
        $user->save();

        $this->assertCount(1, Activity::all());

        $this->assertInstanceOf(get_class($this->user), $this->getLastActivity()->subject);
        $this->assertEquals($user->id, $this->getLastActivity()->subject->id);
        $this->assertEquals($user->id, $this->getLastActivity()->causer->id);
        $this->assertCount(1, $user->activities);
        $this->assertCount(1, $user->actions);
    }

    public function loginWithFakeUser()
    {
        $user = new $this->user();

        $user::find(1);

        $this->be($user);

        return $user;
    }
}
