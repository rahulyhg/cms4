<?php

namespace Botble\Theme\Commands;

use Botble\Setting\Models\Setting;
use DB;
use File;
use Illuminate\Console\Command;

class ThemeInstallSampleDataCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'theme:install-sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install sample data for current active theme';

    /**
     * Execute the console command.
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Processing ...');

        $theme = setting('theme');
        if (!$theme) {
            $theme = array_first(scan_folder(public_path('themes')));
            setting()->set('theme', $theme);
        }

        $content = get_file_data(public_path('themes/' . $theme . '/theme.json'));

        if (!empty($content)) {
            $required_plugins = array_get($content, 'required_plugins', []);
            if (!empty($required_plugins)) {
                $this->info('Activating required plugins ...');
                foreach ($required_plugins as $required_plugin) {
                    $this->info('Activating plugin "' . $required_plugin . '"');
                    $this->call('plugin:activate', ['name' => $required_plugin]);
                }
            }
        }

        $database = public_path('themes/' . $theme . '/data/sample.sql');

        if (File::exists($database)) {
            $this->info('Importing sample data...');
            // Force the new login to be used
            DB::purge();
            DB::unprepared('USE `' . env('DB_DATABASE') . '`');
            DB::connection()->setDatabaseName(env('DB_DATABASE'));
            DB::unprepared(File::get($database));
            $this->info('Done!');
        }

        return true;
    }
}
