Laravel queue walker
===========================
Process all jobs on a queue.

## Features

- Based on queue:work command.
- Once exec all jobs awaiting in queue and end executing
- Don't require dedicated or virtual server to process queues (best way use "php artisan queue:work --daemon")
- Stored "queue:work" options: queue, delay, memory, tries

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Require this package with composer using the following command:

```
composer require salopot/laravel-queue-walker "dev-master"
```

or add

```
"salopot/laravel-queue-walker": "dev-master"
```

to the require section of your `composer.json` file.

After updating composer, add the ServiceProvider to the providers array in config/app.php

```
Salopot\QueueWalker\QueueWalkerServiceProvider::class,
```

Usage
-----

Run artisan command for execute all jobs awaiting in queue:

```
php artisan queue:walk
```

or add call command to laravel scheduler (see: https://laravel.com/docs/5.1/scheduling)

```
$schedule->command('queue:walk')->everyMinute();
```