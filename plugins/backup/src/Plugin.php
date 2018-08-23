<?php

namespace Botble\Backup;

use Artisan;
use Botble\Backup\Providers\BackupServiceProvider;
use Botble\Base\Interfaces\PluginInterface;
use Botble\Base\Supports\Helper;

class Plugin implements PluginInterface
{

    /**
     * @author Sang Nguyen
     */
    public static function activate()
    {

        Artisan::call('vendor:publish', [
            '--force' => true,
            '--tag' => 'public',
            '--provider' => BackupServiceProvider::class,
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
        Helper::removePluginAssets('backup');
    }
}
