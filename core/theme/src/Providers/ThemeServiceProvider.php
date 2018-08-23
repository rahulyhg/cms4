<?php

namespace Botble\Theme\Providers;

use Botble\Theme\Commands\ThemeInstallSampleDataCommand;
use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Theme\Commands\ThemeActivateCommand;
use Botble\Theme\Commands\ThemeCreateCommand;
use Botble\Theme\Commands\ThemeRemoveCommand;
use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Facades\ManagerFacade;
use Botble\Theme\Facades\ThemeFacade;
use Botble\Theme\Facades\ThemeOptionFacade;
use Botble\Theme\Theme;
use Event;
use File;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
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
        AliasLoader::getInstance()->alias('Theme', ThemeFacade::class);
        AliasLoader::getInstance()->alias('ThemeOption', ThemeOptionFacade::class);
        AliasLoader::getInstance()->alias('ThemeManager', ManagerFacade::class);

        $this->app->bind(ThemeContract::class, Theme::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeCreateCommand::class,
                ThemeRemoveCommand::class,
                ThemeActivateCommand::class,
                ThemeInstallSampleDataCommand::class,
            ]);
        }

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/theme')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-theme',
                    'priority' => 1,
                    'parent_id' => 'cms-core-appearance',
                    'name' => trans('core.base::layouts.theme'),
                    'icon' => null,
                    'url' => route('theme.list'),
                    'permissions' => ['theme.list'],
                ])
                ->registerItem([
                    'id' => 'cms-core-theme-option',
                    'priority' => 4,
                    'parent_id' => 'cms-core-appearance',
                    'name' => trans('core.base::layouts.theme_options'),
                    'icon' => null,
                    'url' => route('theme.options'),
                    'permissions' => ['theme.options'],
                ])
                ->registerItem([
                    'id' => 'cms-core-appearance-custom-css',
                    'priority' => 5,
                    'parent_id' => 'cms-core-appearance',
                    'name' => __('Custom CSS'),
                    'icon' => null,
                    'url' => route('theme.custom-css'),
                    'permissions' => ['theme.custom-css'],
                ]);
        });

        $this->app->booted(function () {
            $file = public_path('themes/' . setting('theme') . '/assets/css/style.integration.css');
            if (File::exists($file)) {
                \Theme::asset()->container('after_header')->add('theme-style-integration-css', 'themes/' . setting('theme') . '/assets/css/style.integration.css');
            }
        });
    }
}
