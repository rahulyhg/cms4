<?php

namespace Botble\Base\Providers;

use Botble\Base\Supports\Helper;
use Illuminate\Support\ServiceProvider;

class ThemeManagementServiceProvider extends ServiceProvider
{
    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $theme = setting('theme');
        if (check_database_connection() && !empty($theme)) {
            app('translator')->addJsonPath(public_path('themes/' . $theme . '/lang'));

            Helper::autoload(public_path() . DIRECTORY_SEPARATOR . config('core.theme.general.themeDir') . DIRECTORY_SEPARATOR . $theme . '/functions');
        }
    }
}
