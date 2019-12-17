<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand as Command;
use Illuminate\Support\Str;
use Metallizzer\Package\Traits\Packageable;

class ControllerMakeCommand extends Command
{
    use Packageable;

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        $model = Str::replaceFirst(
            $this->laravel->getNamespace(),
            '',
            parent::parseModel($model)
        );

        if (!Str::startsWith($model, $rootNamespace = $this->package->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }
}
