<?php

namespace Metallizzer\Package\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Metallizzer\Package\Package;
use Metallizzer\Package\PackageException;
use Symfony\Component\Console\Input\InputOption;

trait Packageable
{
    protected $namespace;
    protected $package;

    /**
     * Create a new controller creator command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->name = 'package:'.$this->name;

        $this->description = $this->description.' for your package';

        parent::__construct($files);
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (!$package = trim($this->option('package'))) {
            if (!$packages = $this->getPackagesList()) {
                return $this->error('Could not find any packages.');
            }

            $package = $this->choice('Select package:', $packages);
        }

        try {
            $this->package = new Package($package, $this->files);
        } catch (PackageException $e) {
            return $this->error($e->getMessage());
        }

        if (!$this->files->isDirectory($this->package->path)) {
            return $this->error(sprintf('Package %s not found.', $package));
        }

        $this->package->addToAutoload();

        return parent::handle();
    }

    /**
     * Call another console command.
     *
     * @param \Symfony\Component\Console\Command\Command|string $command
     *
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        if (strpos($command, 'make:') === 0) {
            $command = 'package:'.$command;

            $arguments = array_merge($arguments, [
                '--package' => $this->package->fullName,
            ]);
        }

        return parent::call($command, $arguments);
    }

    /**
     * Returns list of existence packages.
     *
     * @return array
     */
    protected function getPackagesList()
    {
        $packages = [];

        if (!$dirs = glob(config('package.path').'/*/*', GLOB_ONLYDIR)) {
            return $packages;
        }

        $path = realpath(config('package.path'));

        foreach ($dirs as $dir) {
            $name = trim(Str::after(realpath($dir), $path), DIRECTORY_SEPARATOR);

            $packages[] = $name;
        }

        return $packages;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->package->getNamespace();
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $path = parent::getPath($name);

        return Str::replaceFirst($this->laravel['path'], $this->package->path.'/src', $path);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            parent::getOptions(),
            [
                ['package', null, InputOption::VALUE_OPTIONAL, 'The name of the package', null],
            ]
        );
    }
}
