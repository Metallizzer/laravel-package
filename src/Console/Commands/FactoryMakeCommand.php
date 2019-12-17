<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as Command;
use Illuminate\Support\Str;
use Metallizzer\Package\Traits\Packageable;

class FactoryMakeCommand extends Command
{
    use Packageable;

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

        return Str::replaceFirst(
            $this->laravel->databasePath(),
            $this->package->path.'/database',
            $path
        );
    }
}
