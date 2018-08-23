<?php

namespace Botble\Theme\Commands;

use Illuminate\Console\Command;
use Setting;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Input\InputOption;

class ThemeActivateCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a theme';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var File
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @author Teepluss <admin@laravel.in.th>
     */
    public function __construct(Repository $config, File $files)
    {
        $this->config = $config;

        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @author Sang Nguyen
     */
    public function handle()
    {
        if (!$this->files->isDirectory($this->getPath(null))) {
            $this->error('Theme "' . $this->getTheme() . '" is not exists.');
            return false;
        }

        Setting::set('theme', $this->argument('name'));
        Setting::save();
        $this->info('Activate theme ' . $this->argument('name') . ' successfully!');
        $this->call('cache:clear');
        return true;
    }


    /**
     * Get the theme name.
     *
     * @return string
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getTheme()
    {
        return strtolower($this->argument('name'));
    }

    /**
     * Get root writable path.
     *
     * @param  string $path
     * @return string
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getPath($path)
    {
        $rootPath = $this->option('path');

        return $rootPath . '/' . strtolower($this->getTheme()) . '/' . $path;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'Name of the theme to activate.',
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getOptions()
    {
        $path = public_path() . '/' . $this->config->get('core.theme.general.themeDir');

        return [
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path to theme directory.', $path,
            ],
        ];
    }
}
