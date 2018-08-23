<?php

namespace Botble\Setting\Providers;

use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\Facades\SettingFacade;
use Botble\Setting\Models\Setting as SettingModel;
use Botble\Setting\Repositories\Caches\SettingCacheDecorator;
use Botble\Setting\Repositories\Eloquent\SettingRepository;
use Botble\Setting\Repositories\Interfaces\SettingInterface;
use Botble\Support\Services\Cache\Cache;
use Event;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @author Sang Nguyen
     */
    public function register()
    {
        AliasLoader::getInstance()->alias('Setting', SettingFacade::class);

        Helper::autoload(__DIR__ . '/../../helpers');

        if (function_exists('setting') && setting('enable_cache', false)) {
            $this->app->singleton(SettingInterface::class, function () {
                return new SettingCacheDecorator(new SettingRepository(new SettingModel()), new Cache($this->app['cache'], SettingRepository::class));
            });
        } else {
            $this->app->singleton(SettingInterface::class, function () {
                return new SettingRepository(new SettingModel());
            });
        }
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/setting')
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssetsFolder()
            ->publishPublicFolder();

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-settings',
                    'priority' => 998,
                    'parent_id' => null,
                    'name' => trans('core.setting::setting.title'),
                    'icon' => 'fa fa-cogs',
                    'url' => route('settings.options'),
                    'permissions' => ['settings.options'],
                ])
                ->registerItem([
                    'id' => 'cms-core-settings-general',
                    'priority' => 1,
                    'parent_id' => 'cms-core-settings',
                    'name' => trans('core.base::layouts.setting_general'),
                    'icon' => null,
                    'url' => route('settings.options'),
                    'permissions' => ['settings.options'],
                ]);
        });
    }
}
