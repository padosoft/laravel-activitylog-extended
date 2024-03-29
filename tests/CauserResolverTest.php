<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test;

use Auth;
use Padosoft\Laravel\ActivitylogExtended\Models\Activity;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\User;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\UserWithRelations;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\Article;
use Padosoft\Laravel\ActivitylogExtended\Test\Models\ArticleWithRelations;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Exceptions\CouldNotLogActivity;
use Spatie\Activitylog\Facades\CauserResolver;

class CauserResolverTest extends TestCase
{
    /** @test */
    public function it_can_resolve_current_logged_in_user()
    {
        Auth::login($user = User::first());

        $causer = CauserResolver::resolve();

        $this->assertInstanceOf(User::class, $causer);
        $this->assertEquals($causer->id, $user->id);
    }

    /** @test */
    public function it_will_throw_an_exception_if_it_cannot_resolve_user_by_id()
    {
        $this->expectException(CouldNotLogActivity::class);

        CauserResolver::resolve(9999);
    }

    /** @test */
    public function it_can_resloved_user_from_passed_id()
    {
        $causer = CauserResolver::resolve(1);

        $this->assertInstanceOf(User::class, $causer);
        $this->assertEquals(1, $causer->id);
    }

    /** @test */
    public function it_will_resolve_the_provided_override_callback()
    {
        CauserResolver::resolveUsing(fn() => Article::first());

        $causer = CauserResolver::resolve();

        $this->assertInstanceOf(Article::class, $causer);
        $this->assertEquals(1, $causer->id);
    }

    /** @test */
    public function it_will_resolve_any_model()
    {
        $causer = CauserResolver::resolve($article = Article::first());

        $this->assertInstanceOf(Article::class, $causer);
        $this->assertEquals($article->id, $causer->id);
    }
}
