<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Routing\Console\MiddlewareMakeCommand as Command;
use Metallizzer\Package\Traits\Packageable;

class MiddlewareMakeCommand extends Command
{
    use Packageable;
}
