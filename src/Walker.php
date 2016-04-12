<?php

namespace Salopot\QueueWalker;

use Exception;
use Throwable;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class Walker extends \Illuminate\Queue\Worker
{

    /**
     * Run the next job for the daemon worker.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  int  $delay
     * @param  int  $sleep
     * @param  int  $maxTries
     * @return void
     */
    protected function runNextJobForDaemon($connectionName, $queue, $delay, $sleep, $maxTries)
    {
        try {
            $data = $this->pop($connectionName, $queue, $delay, $sleep, $maxTries);
            if ($data['job'] === null) $this->stop();
        } catch (Exception $e) {
            if ($this->exceptions) {
                $this->exceptions->report($e);
            }
        } catch (Throwable $e) {
            if ($this->exceptions) {
                $this->exceptions->report(new FatalThrowableError($e));
            }
        }
    }

}