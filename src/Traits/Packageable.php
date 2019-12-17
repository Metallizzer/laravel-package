<?php

namespace Metallizzer\Package\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Metallizzer\Package\Package;
use Metallizzer\Package\PackageException;
use Symfony\Component\Console\Input\InputArgument;

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
        $package = trim($this->argument('package'));

        try {
            $this->package = new Package($package, $this->files);
        } catch (PackageException $e) {
            return $this->error($e->getMessage());
        }

        if (!$this->files->isDirectory($this->package->path)) {
            return $this->error(sprintf('Package %s not found.', $package));
        }

        $this->package->checkAutoload(true);

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

            $arguments = array_merge([
                'package' => $this->package->fullName,
            ], $arguments);
        }

        return parent::call($command, $arguments);
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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge([
            ['package', InputArgument::REQUIRED, 'The name of the package'],
        ], parent::getArguments());
    }
}
