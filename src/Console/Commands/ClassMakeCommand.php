<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Metallizzer\Package\Traits\Packageable;

class ClassMakeCommand extends GeneratorCommand
{
    use Packageable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'package:make:class';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new class for your package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/class.stub';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function qualifyClass($name)
    {
        $namespace = $this->package->getNamespace();

        $name = parent::qualifyClass($name);
        $name = Str::after($name, $namespace);
        $name = implode('\\', array_map([Str::class, 'studly'], explode('\\', $name)));

        return $namespace.$name;
    }
}
