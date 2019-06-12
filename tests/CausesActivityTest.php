<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Padosoft\Laravel\ActivitylogExtended\Test\Models\User;

class CausesActivityTest extends TestCase
{
    /** @test */
    public function it_can_get_all_activity_for_the_causer()
    {
        $causer = User::first();

        activity()->by($causer)->log('perform activity');
        activity()->by($causer)->log('perform another activity');

        $this->assertCount(2, $causer->actions);
    }
}
