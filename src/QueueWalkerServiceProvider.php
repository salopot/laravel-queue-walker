<?php
namespace Salopot\QueueWalker;

use Salopot\QueueWalker\Console\WalkCommand;

class QueueWalkerServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function register()
    {
        $this->app->bind('walker', 'Salopot\QueueWalker\Walker');
        $this->app->singleton('command.queue-walker.walk', function($app) {
            return new WalkCommand($app['walker']);
        });
        $this->commands(['command.queue-walker.walk']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.queue-walker.walk'
        ];
    }

}