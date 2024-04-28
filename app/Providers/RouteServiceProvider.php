<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(600)
                ->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'))
                ->group(base_path('/routes/UserRoutes/UserRoutes.php'))
                ->group(base_path('/routes/Nomenclature/NomenclatureRoutes.php'))
                ->group(base_path('/routes/Cars/BaseCarsRoutes.php'))
                ->group(base_path('/routes/Cars/CreateNewCar.php'))
                ->group(base_path('/routes/Cars/AllCars.php'))
                ->group(base_path('/routes/Cars/AvailableCarsRoutes.php'))
                ->group(base_path('/routes/Import/ImportRoutes.php'))
                ->group(base_path('/routes/Directories/ContrAgentRoutes.php'))
                ->group(base_path('/routes/SettingsRoutes/SettingsRoutes.php'))
                ->group(base_path('/routes/Auth/auth.php'))
                ->group(base_path('/routes/Public/Nomenclature/PublicNomenclatureRoutes.php'))
                ->group(base_path('/routes/Website/WebsiteRoutes.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
