<?php

namespace Botble\Base\Providers;

use Botble\ACL\Providers\AclServiceProvider;
use Botble\Assets\Providers\AssetsServiceProvider;
use Botble\Base\Charts\Supports\ChartBuilder;
use Botble\Base\Facades\ChartBuilderFacade;
use Botble\Base\Events\SessionStarted;
use Botble\Base\Exceptions\Handler;
use Botble\Base\Facades\ActionFacade;
use Botble\Base\Facades\AdminBarFacade;
use Botble\Base\Facades\AdminBreadcrumbFacade;
use Botble\Base\Facades\DashboardMenuFacade;
use Botble\Base\Facades\EmailHandlerFacade;
use Botble\Base\Facades\FilterFacade;
use Botble\Base\Facades\JsonFeedManagerFacade;
use Botble\Base\Facades\MailVariableFacade;
use Botble\Base\Facades\MetaBoxFacade;
use Botble\Base\Facades\PageTitleFacade;
use Botble\Base\Facades\SiteMapManagerFacade;
use Botble\Base\Http\Middleware\AdminBarMiddleware;
use Botble\Base\Http\Middleware\DisableInDemoMode;
use Botble\Base\Http\Middleware\HttpsProtocol;
use Botble\Base\Http\Middleware\Locale;
use Botble\Base\Http\Middleware\StartSession;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Repositories\Caches\MetaBoxCacheDecorator;
use Botble\Base\Repositories\Eloquent\MetaBoxRepository;
use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Dashboard\Providers\DashboardServiceProvider;
use Botble\Media\Providers\MediaServiceProvider;
use Botble\Menu\Providers\MenuServiceProvider;
use Botble\Optimize\Providers\OptimizeServiceProvider;
use Botble\Page\Providers\PageServiceProvider;
use Botble\SeoHelper\Providers\SeoHelperServiceProvider;
use Botble\Setting\Providers\SettingServiceProvider;
use Botble\Shortcode\Providers\ShortcodeServiceProvider;
use Botble\Slug\Providers\SlugServiceProvider;
use Botble\Support\Providers\SupportServiceProvider;
use Botble\Support\Services\Cache\Cache;
use Botble\Table\Providers\TableServiceProvider;
use Botble\Theme\Providers\ThemeServiceProvider;
use Botble\Widget\Providers\WidgetServiceProvider;
use Event;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use MetaBox;
use Schema;

class BaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Register any application services.
     *
     * @return void
     * @author Sang Nguyen
     */
    public function register()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/base')
            ->loadAndPublishConfigurations(['general']);

        Helper::autoload(__DIR__ . '/../../helpers');

        $this->app->register(SupportServiceProvider::class);
        $this->app->register(AssetsServiceProvider::class);
        $this->app->register(SettingServiceProvider::class);
        $this->app->register(ShortcodeServiceProvider::class);
        $this->app->register(TableServiceProvider::class);

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->pushMiddlewareToGroup('web', Locale::class);
        $router->pushMiddlewareToGroup('web', HttpsProtocol::class);
        $router->pushMiddlewareToGroup('web', AdminBarMiddleware::class);
        $router->pushMiddlewareToGroup('web', StartSession::class);
        $router->aliasMiddleware('preventDemo', DisableInDemoMode::class);

        $this->app->bind('chart-builder', function (Container $container) {
            return new ChartBuilder($container);
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('MetaBox', MetaBoxFacade::class);
        $loader->alias('Action', ActionFacade::class);
        $loader->alias('Filter', FilterFacade::class);
        $loader->alias('EmailHandler', EmailHandlerFacade::class);
        $loader->alias('AdminBar', AdminBarFacade::class);
        $loader->alias('PageTitle', PageTitleFacade::class);
        $loader->alias('AdminBreadcrumb', AdminBreadcrumbFacade::class);
        $loader->alias('DashboardMenu', DashboardMenuFacade::class);
        $loader->alias('SiteMapManager', SiteMapManagerFacade::class);
        $loader->alias('JsonFeedManager', JsonFeedManagerFacade::class);
        $loader->alias('ChartBuilder', ChartBuilderFacade::class);
        $loader->alias('MailVariable', MailVariableFacade::class);

        if (setting('enable_cache', false)) {
            $this->app->singleton(MetaBoxInterface::class, function () {
                return new MetaBoxCacheDecorator(new MetaBoxRepository(new MetaBoxModel()), new Cache($this->app['cache'], MetaBoxRepository::class));
            });
        } else {
            $this->app->singleton(MetaBoxInterface::class, function () {
                return new MetaBoxRepository(new MetaBoxModel());
            });
        }

        $this->app->register(PluginServiceProvider::class);
    }

    /**
     * Boot the service provider.
     * @return void
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->app->register(AclServiceProvider::class);
        $this->app->register(DashboardServiceProvider::class);
        $this->app->register(MediaServiceProvider::class);
        $this->app->register(MenuServiceProvider::class);
        $this->app->register(PageServiceProvider::class);
        $this->app->register(SeoHelperServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
        $this->app->register(WidgetServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
        $this->app->register(ThemeManagementServiceProvider::class);
        $this->app->register(BreadcrumbsServiceProvider::class);
        $this->app->register(ComposerServiceProvider::class);
        $this->app->register(OptimizeServiceProvider::class);
        $this->app->register(SlugServiceProvider::class);
        $this->app->register(MailConfigServiceProvider::class);

        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/base')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssetsFolder()
            ->publishPublicFolder();

        Schema::defaultStringLength(191);

        $this->app->register(FormServiceProvider::class);

        $this->app->booted(function () {
            do_action('init');
            add_action(BASE_ACTION_META_BOXES, [MetaBox::class, 'doMetaBoxes'], 8, 3);

            if (check_database_connection() && Schema::hasTable('settings')) {
                $theme = setting('theme');
                if (!$theme) {
                    setting()->set('theme', array_first(scan_folder(public_path('themes'))));
                } else {
                    $this->app->get('translator')->addJsonPath(public_path('themes/' . $theme . '/lang'));
                }
            }
        });

        Event::listen(SessionStarted::class, function () {
            $this->registerDefaultMenus();
        });
    }

    /**
     * Add default dashboard menu for core
     * @author Sang Nguyen
     */
    public function registerDefaultMenus()
    {
        dashboard_menu()
            ->registerItem([
                'id' => 'cms-core-dashboard',
                'priority' => 0,
                'parent_id' => null,
                'name' => trans('core.base::layouts.dashboard'),
                'icon' => 'fa fa-home',
                'url' => route('dashboard.index'),
                'permissions' => ['dashboard.index'],
            ])
            ->registerItem([
                'id' => 'cms-core-appearance',
                'priority' => 996,
                'parent_id' => null,
                'name' => trans('core.base::layouts.appearance'),
                'icon' => 'fa fa-paint-brush',
                'url' => route('theme.list'),
                'permissions' => ['theme.list', 'menus.list', 'widgets.list', 'theme.options'],
            ])
            ->registerItem([
                'id' => 'cms-core-menu',
                'priority' => 2,
                'parent_id' => 'cms-core-appearance',
                'name' => trans('core.base::layouts.menu'),
                'icon' => null,
                'url' => route('menus.list'),
                'permissions' => ['menus.list'],
            ])
            ->registerItem([
                'id' => 'cms-core-plugins',
                'priority' => 997,
                'parent_id' => null,
                'name' => trans('core.base::layouts.plugins'),
                'icon' => 'fa fa-plug',
                'url' => route('plugins.list'),
                'permissions' => ['plugins.list'],
            ])
            ->registerItem([
                'id' => 'cms-core-platform-administration',
                'priority' => 999,
                'parent_id' => null,
                'name' => trans('core.base::layouts.platform_admin'),
                'icon' => 'fa fa-shield',
                'url' => null,
                'permissions' => ['users.list'],
            ])
            ->registerItem([
                'id' => 'cms-core-system-information',
                'priority' => 5,
                'parent_id' => 'cms-core-platform-administration',
                'name' => trans('core.base::layouts.system_information'),
                'icon' => null,
                'url' => route('system.info'),
                'permissions' => ['superuser'],
            ])
            ->registerItem([
                'id' => 'cms-core-system-cache',
                'priority' => 6,
                'parent_id' => 'cms-core-platform-administration',
                'name' => trans('core.base::cache.cache_management'),
                'icon' => null,
                'url' => route('system.cache'),
                'permissions' => ['superuser'],
            ]);

        if (config('core.base.general.allow_config_mail_server_from_admin')) {
            dashboard_menu()->registerItem([
                'id' => 'cms-core-settings-email',
                'priority' => 2,
                'parent_id' => 'cms-core-settings',
                'name' => trans('core.base::layouts.setting_email'),
                'icon' => null,
                'url' => route('settings.email'),
                'permissions' => ['settings.email'],
            ]);
        }
    }
}
