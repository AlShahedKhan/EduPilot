<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddlewareTest extends MockeryTestCase
{
    public function test_unauthenticated_requests_cannot_reach_protected_routes(): void
    {
        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->once()->andReturnFalse();

        $auth = Mockery::mock(AuthFactory::class);
        $auth->shouldReceive('guard')->once()->with(null)->andReturn($guard);

        $middleware = new class($auth) extends Authenticate
        {
            protected function redirectTo($request)
            {
                return '/login';
            }
        };

        $redirector = Mockery::mock(Redirector::class);
        $redirector->shouldReceive('guest')
            ->zeroOrMoreTimes()
            ->with('/login')
            ->andReturn(new RedirectResponse('/login'));

        $originalContainer = Container::getInstance();
        $container = new Container();
        $container->instance('redirect', $redirector);
        Container::setInstance($container);

        $this->expectException(AuthenticationException::class);

        try {
            $middleware->handle(
                Request::create('/protected'),
                static fn (): Response => new Response('protected content'),
            );
        } finally {
            Container::setInstance($originalContainer);
        }
    }
}
