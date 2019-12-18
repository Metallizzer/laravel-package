<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as Command;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Metallizzer\Package\Traits\Packageable;

class MigrateMakeCommand extends Command
{
    use Packageable;

    protected $files;

    /**
     * Create a new migration install command instance.
     *
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        $this->signature = str_replace([
            'make:migration',
            '{--create=',
        ], [
            'package:make:migration',
            '{--package= : The name of the package}'.PHP_EOL.'{--create=',
        ], $this->signature);

        $this->description = $this->description.' for your package';

        $this->files = app('files');

        parent::__construct($creator, $composer);
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $path = Str::replaceFirst(
            $this->laravel->basePath(),
            $this->package->path,
            parent::getMigrationPath()
        );

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
