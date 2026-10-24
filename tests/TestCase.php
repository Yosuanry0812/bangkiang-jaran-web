<?php

namespace Tests;

use App\Http\Middleware\EnsureExplicitLogin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs(Authenticatable $user, $guard = null)
    {
        if ($guard === null || $guard === 'web') {
            $this->withSession([EnsureExplicitLogin::SESSION_KEY => (string) $user->getAuthIdentifier()]);
        }

        return parent::actingAs($user, $guard);
    }
}
