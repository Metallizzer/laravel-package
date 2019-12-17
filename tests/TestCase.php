<?php

namespace Metallizzer\Package\Tests;

use Illuminate\Support\Facades\File;
use Metallizzer\Package\Providers\PackageServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function tempPath($path = ''): string
    {
        return __DIR__.'/temp'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function initializeTempDirectory()
    {
        $this->initializeDirectory($this->tempPath());
    }

    public function initializeDirectory(string $directory)
    {
        File::deleteDirectory($directory);
        File::makeDirectory($directory);

        $this->addGitignoreTo($directory);
    }

    public function removeTempDirectory()
    {
        return File::deleteDirectory($this->tempPath());
    }

    public function addGitignoreTo(string $directory)
    {
        $fileName     = "{$directory}/.gitignore";
        $fileContents = '*'.PHP_EOL.'!.gitignore';

        File::put($fileName, $fileContents);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PackageServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeTempDirectory();

        config()->set('package.path', $this->tempPath());
    }
}
