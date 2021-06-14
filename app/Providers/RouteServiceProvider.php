<?php

namespace App\Providers;

use App\Models\Site;
use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            $this->mapSiteRoutes();
        });
    }

    private function mapSiteRoutes()
    {


        try {

            if (Schema::hasTable('sites') && $site = Site::getInstance()) { // can be null when migrating

                // routes shared for all sites
                $domain = get_domain();
                Route::middleware('web')->namespace($this->namespace)->domain($domain)->group(base_path('routes/web-shared.php'));

                $siteRoutesPath = "routes/sites/$site->identifier.php";

                if (file_exists(base_path($siteRoutesPath))) {
                    // these routes overrides web-shared routes
                    Route::middleware('web')->namespace($this->namespace)->domain($domain)->group(base_path($siteRoutesPath));
                }
            }

        } catch (QueryException $exception) {
            // ignore - initial migrations not yet found on tables
        }

    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
