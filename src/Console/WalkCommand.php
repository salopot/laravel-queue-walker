<?php

namespace Salopot\QueueWalker\Console;

use Salopot\QueueWalker\Walker;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Job;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WalkCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:walk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all jobs on a queue';

    /**
     * The queue walker instance.
     *
     * @var \App\Console\Walker
     */
    protected $walker;

    /**
     * Create a new queue listen command.
     *
     * @param  \App\Console\Walker  $walker
     * @return void
     */
    public function __construct(Walker $walker)
    {
        parent::__construct();

        $this->walker = $walker;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $queue = $this->option('queue');

        $delay = $this->option('delay');

        // The memory limit is the amount of memory we will allow the script to occupy
        // before killing it and letting a process manager restart it for us, which
        // is to protect us against any memory leaks that will be in the scripts.
        $memory = $this->option('memory');

        $connection = $this->argument('connection');

        $response = $this->runWalker(
            $connection, $queue, $delay, $memory
        );

        // If a job was fired by the walker, we'll write the output out to the console
        // so that the developer can watch live while the queue runs in the console
        // window, which will also of get logged if stdout is logged out to disk.
        if (! is_null($response['job'])) {
            $this->writeOutput($response['job'], $response['failed']);
        }
    }

    /**
     * Run the walker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  int  $delay
     * @param  int  $memory
     * @return array
     */
    protected function runWalker($connection, $queue, $delay, $memory)
    {
        $this->walker->setCache($this->laravel['cache']->driver());

        $this->walker->setDaemonExceptionHandler(
            $this->laravel['Illuminate\Contracts\Debug\ExceptionHandler']
        );

        return $this->walker->daemon(
            $connection, $queue, $delay, $memory, 0, $this->option('tries')
        );
    }

    /**
     * Write the status output for the queue walker.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  bool  $failed
     * @return void
     */
    protected function writeOutput(Job $job, $failed)
    {
        if ($failed) {
            $this->output->writeln('<error>Failed:</error> '.$job->getName());
        } else {
            $this->output->writeln('<info>Processed:</info> '.$job->getName());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['connection', InputArgument::OPTIONAL, 'The name of connection', null],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on'],

            ['delay', null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0],

            ['memory', null, InputOption::VALUE_OPTIONAL, 'The memory limit in megabytes', 128],

            ['tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0],
        ];
    }
}
