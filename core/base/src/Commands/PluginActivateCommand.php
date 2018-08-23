<?php

namespace Botble\Base\Commands;

use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PluginActivateCommand extends Command
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:activate {name : The plugin that you want to activate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a plugin in /plugins directory';

    /**
     * Create a new key generator command.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @author Sang Nguyen
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * @throws Exception
     * @return boolean
     * @author Sang Nguyen
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        $plugin = strtolower($this->argument('name'));
        $location = config('core.base.general.plugin_path') . '/' . $plugin;

        if (!$this->files->isDirectory($location)) {
            $this->error('This plugin is not exists.');
            return false;
        }

        $content = get_file_data($location . '/plugin.json');
        if (!empty($content)) {
            $activated_plugins = get_active_plugins();
            if (!in_array($plugin, $activated_plugins)) {

                if (!empty(array_get($content, 'require'))) {
                    $valid = count(array_intersect($content['require'], $activated_plugins)) == count($content['require']);
                    if (!$valid) {
                        $this->error('<info>Please activate plugin(s): ' . implode(',', $content['require']) . ' before activate this plugin!</info>');
                        return false;
                    }
                }

                if (!class_exists($content['provider'])) {
                    $loader = new ClassLoader();
                    $loader->setPsr4($content['namespace'], base_path('plugins/' . $plugin . '/src'));
                    $loader->register(true);
                }

                if (class_exists($content['namespace'] . 'Plugin')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'activate']);
                }

                setting()->set('activated_plugins', json_encode(array_values(array_merge($activated_plugins, [$plugin]))));
                setting()->save();

                cache()->forget(md5('cache-dashboard-menu'));

                $this->line('<info>Activate plugin successfully!</info>');
            } else {
                $this->line('<info>This plugin is activated already!</info>');
            }
        }
        return true;
    }
}
