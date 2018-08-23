<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install CMS';

    /**
     * Execute the console command.
     * @author Sang Nguyen
     */
    public function handle()
    {
        $this->info('Starting installation...');
        $this->call('migrate:fresh');
        $this->call('user:create');

        setting()->set('site_title', __('A site using Botble CMS'));
        setting()->save();

        return true;
    }
}
