<?php

namespace Botble\Language;

use Artisan;
use Botble\Base\Interfaces\PluginInterface;
use Botble\Base\Supports\Helper;
use Botble\Language\Providers\LanguageServiceProvider;
use Schema;

class Plugin implements PluginInterface
{

    /**
     * @author Sang Nguyen
     */
    public static function activate()
    {
        Artisan::call('migrate', [
            '--force' => true,
            '--path' => 'plugins/language/database/migrations',
        ]);

        Artisan::call('vendor:publish', [
            '--force' => true,
            '--tag' => 'public',
            '--provider' => LanguageServiceProvider::class,
        ]);
    }

    /**
     * @author Sang Nguyen
     */
    public static function deactivate()
    {
    }

    /**
     * @author Sang Nguyen
     */
    public static function remove()
    {
        Helper::removePluginAssets('language');

        Schema::dropIfExists('languages');
        Schema::dropIfExists('language_meta');
    }
}
