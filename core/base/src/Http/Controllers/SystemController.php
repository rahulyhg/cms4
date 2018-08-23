<?php

namespace Botble\Base\Http\Controllers;

use Artisan;
use Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\SystemManagement;
use Botble\Base\Tables\InfoTable;
use Botble\Table\TableBuilder;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SystemController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getInfo(Request $request, TableBuilder $tableBuilder)
    {
        page_title()->setTitle(trans('core.base::system.info.title'));

        Assets::addAppModule(['system-info'])
            ->addStylesheetsDirectly(['vendor/core/css/system-info.css']);

        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        $infoTable = $tableBuilder->create(InfoTable::class);

        if ($request->expectsJson()) {
            return $infoTable->renderTable();
        }

        $systemEnv = SystemManagement::getSystemEnv();
        $serverEnv = SystemManagement::getServerEnv();
        $serverExtras = SystemManagement::getServerExtras();
        $systemExtras = SystemManagement::getSystemExtras();
        $extraStats = SystemManagement::getExtraStats();
        return view('core.base::system.info', compact('packages', 'infoTable', 'systemEnv', 'serverEnv', 'extraStats', 'serverExtras', 'systemExtras'));
    }

    /**
     * Show all plugins in system
     *
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getListPlugins()
    {
        page_title()->setTitle(trans('core.base::system.plugins'));

        if (File::exists(base_path('plugins/.DS_Store'))) {
            File::delete(base_path('plugins/.DS_Store'));
        }
        $plugins = scan_folder(base_path('plugins'));
        foreach ($plugins as $plugin) {
            if (File::exists(base_path('plugins/' . $plugin . '/.DS_Store'))) {
                File::delete(base_path('plugins/' . $plugin . '/.DS_Store'));
            }
        }

        Assets::addAppModule(['plugin']);

        $plugins = scan_folder(config('core.base.general.plugin_path'));
        if (!empty($plugins)) {
            $installed = get_active_plugins();
            foreach ($plugins as $plugin) {
                $plugin_path = config('core.base.general.plugin_path') . DIRECTORY_SEPARATOR . $plugin;
                $content = get_file_data($plugin_path . '/plugin.json');
                if (!empty($content)) {
                    if (!in_array($plugin, $installed)) {
                        $content['status'] = 0;
                    } else {
                        $content['status'] = 1;
                    }

                    $content['path'] = $plugin;
                    $content['image'] = null;
                    if (File::exists($plugin_path . '/screenshot.png')) {
                        $content['image'] = base64_encode(File::get($plugin_path . '/screenshot.png'));
                    }
                    $list[] = (object)$content;
                }
            }
        }
        return view('core.base::plugins.list', compact('list'));
    }

    /**
     * Activate or Deactivate plugin
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return mixed
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getChangePluginStatus(Request $request, BaseHttpResponse $response)
    {
        $plugin = strtolower($request->input('alias'));

        $content = get_file_data(config('core.base.general.plugin_path') . DIRECTORY_SEPARATOR . $plugin . '/plugin.json');
        if (!empty($content)) {

            try {
                $activated_plugins = get_active_plugins();
                if (!in_array($plugin, $activated_plugins)) {
                    if (!empty(array_get($content, 'require'))) {
                        $valid = count(array_intersect($content['require'], $activated_plugins)) == count($content['require']);
                        if (!$valid) {
                            return $response->setError(true)->setMessage(trans('core.base::system.missing_required_plugins', ['plugins' => implode(',', $content['require'])]));
                        }
                    }

                    Artisan::call('plugin:activate', ['name' => $plugin]);
                    if (class_exists($content['namespace'] . 'Plugin')) {
                        call_user_func([$content['namespace'] . 'Plugin', 'activate']);
                    }
                } else {
                    Artisan::call('plugin:deactivate', ['name' => $plugin]);
                    if (class_exists($content['namespace'] . 'Plugin')) {
                        call_user_func([$content['namespace'] . 'Plugin', 'deactivate']);
                    }
                }

                return $response->setMessage(trans('core.base::system.update_plugin_status_success'));
            } catch (Exception $ex) {
                info($ex->getMessage());
                return $response->setError(true)->setMessage($ex->getMessage());
            }
        }
        return $response->setError(true)->setMessage(trans('core.base::system.invalid_plugin'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     */
    public function getCacheManagement()
    {
        Assets::addAppModule(['cache']);
        return view('core.base::system.cache');
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postClearCache(Request $request, BaseHttpResponse $response)
    {
        switch ($request->input('type')) {
            case 'clear_cms_cache':
                Artisan::call('cache:clear');
                break;
            case 'refresh_compiled_views':
                Artisan::call('view:clear');
                break;
            case 'clear_config_cache':
                Artisan::call('config:clear');
                break;
            case 'clear_route_cache':
                Artisan::call('route:clear');
                break;
            case 'clear_log':
                Artisan::call('log:clear');
                break;
        }

        return $response->setMessage(trans('core.base::cache.commands.' . $request->input('type') . '.success_msg'));
    }
}
