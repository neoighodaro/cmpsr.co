<?php

namespace Cmpsr\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ComposerInstall extends Command
{
    /**
     * @var string
     */
    protected $signature = 'composer:install {container : The hash of the composer file}';

    /**
     * @var string
     */
    protected $description = 'Runs the install command for a package.';

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $localStorage;

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->storage = Storage::disk('packages');
        $this->localStorage = Storage::disk('local');
    }

    /**
     * @return void
     */
    public function handle()
    {
        $path = $this->argument('container');

        if ($this->validated($path)) {
            $this->mark($path);
            $this->composerInstall($path);
            $this->zipPackages($path);
            $this->unmark($path);
        }
    }

    /**
     * @param  string $path
     * @return bool
     */
    protected function validated(string $path): bool
    {
        if ($this->localStorage->exists("public/{$path}.zip")) {
            $this->error('Package already exists.');
            return false;
        }

        if (!$this->storage->exists("{$path}/composer.json")) {
            $this->error('Unable to find the package.');
            return false;
        }

        if ($this->storage->exists("{$path}/running")) {
            $this->error('Currently running command in this package.');
            return false;
        }

        return true;
    }

    /**
     * @param  string $path
     * @return void
     */
    protected function composerInstall(string $path): void
    {
        $process = new Process('composer install', $this->storage->path($path));
        $process->setIdleTimeout(60);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            logger()->info($process->getErrorOutput());
        }
    }

    /**
     * @param  string $path
     * @return void
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function zipPackages(string $path): void
    {
        $fullPath = $this->storage->path($path);

        $process = new Process(['zip', '-r', "{$path}.zip", 'vendor'], $fullPath);
        $process->setIdleTimeout(60);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->unmark($path);
            throw new ProcessFailedException($process);
        }

        // Move the zip to the packages root...
        $this->localStorage->move("packages/{$path}/{$path}.zip", "public/{$path}.zip");

        // Remove the directory...
        (new Process(['rm', '-r', $path], $this->storage->path('./')))->run();
    }

    /**
     * @param  string $path
     * @return void
     */
    protected function mark(string $path): void
    {
        $this->storage->put("${path}/running", $path);
    }

    /**
     * @param  string $path
     * @return void
     */
    protected function unmark(string $path): void
    {
        $this->storage->delete("${path}/running");
    }
}
