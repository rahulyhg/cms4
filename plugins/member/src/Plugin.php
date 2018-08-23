<?php

namespace Botble\Member;

use Artisan;
use Botble\Base\Interfaces\PluginInterface;
use Botble\Base\Supports\Helper;
use Botble\Member\Providers\MemberServiceProvider;
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
            '--path' => 'plugins/member/database/migrations',
        ]);

        Artisan::call('vendor:publish', [
            '--force' => true,
            '--tag' => 'public',
            '--provider' => MemberServiceProvider::class,
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
        Helper::removePluginAssets('member');

        Schema::dropIfExists('members');
        Schema::dropIfExists('member_password_resets');
    }
}
