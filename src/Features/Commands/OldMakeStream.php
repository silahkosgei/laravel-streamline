<?php

namespace Iankibet\Streamline\Features\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class OldMakeStream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamline:make-stream {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Streamline class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        Artisan::call('make:stream', ['name' => $name]);
        return 0;
    }
}
