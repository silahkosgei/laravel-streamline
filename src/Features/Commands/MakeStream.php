<?php

namespace Iankibet\Streamline\Features\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeStream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:stream {name}';

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

        $namespace = config('streamline.class_namespace', 'App\\Streams');
        $postfix = config('streamline.class_postfix', 'Stream');

        // Parse input name to determine namespace and class name
        $pathParts = explode('/', $name);
        $className = array_pop($pathParts);
        $relativeNamespace = implode('\\', $pathParts);
        $fullNamespace = trim($namespace . '\\' . $relativeNamespace, '\\');
        $streamsFolder = str_replace('\\', '/', $namespace);
        $streamsFolder = str_replace('App/', '', $streamsFolder);
        // Define file path
        $directory = app_path($streamsFolder.'/' . implode('/', $pathParts));
        // replace postfix from class name, if it appears at the end
        $className = str_replace($postfix.'-*', '', $className.'-*');
        $className = str_replace('-*', '', $className);

        $filePath = $directory . '/' . $className . $postfix . '.php';

        // Ensure the directory exists
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Check if file already exists
        if (File::exists($filePath)) {
            $this->error('Streamline class already exists!');
            return 1;
        }

        // Generate file content using a stub
        $stub = File::get(__DIR__ . '/../../../stubs/streamline.stub');
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$fullNamespace, $className . $postfix],
            $stub
        );

        // Write to file
        File::put($filePath, $content);
        $this->info('Streamline class created successfully at ' . $filePath);

        return 0;
    }
}
