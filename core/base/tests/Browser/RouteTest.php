<?php

namespace Botble\Base\Tests\Browser;

use Auth;
use Botble\Base\Traits\PrepareTestDataTrait;
use Illuminate\Routing\Route;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RouteTest extends DuskTestCase
{
    use PrepareTestDataTrait;

    /**
     * A basic functional test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testGetRoutes()
    {
        $user = $this->user;
        Auth::login($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id);

            $routes = collect(app('router')->getRoutes())
                ->map(function (Route $route) {
                    if (!in_array('GET', $route->methods())) {
                        return false;
                    }
                    if (str_contains($route->uri, '{') || str_contains($route->getName(), 'debugbar')) {
                        return false;
                    }
                    return $route->getName();
                })
                ->reject(function ($item) {
                    return empty($item);
                })
                ->all();

            foreach ($routes as $route) {
                $response = $this->call('GET', route($route));
                $this->followingRedirects()->assertEquals(200, $response->status(), route($route) . ' did not return a 200');
            }
        });
    }
}
