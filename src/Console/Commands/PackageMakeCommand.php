<?php

namespace Metallizzer\Package\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Metallizzer\Package\Package;

class PackageMakeCommand extends Command
{
    /**
     * The package instance.
     *
     * @var \Metallizzer\Package\Package
     */
    protected $package;

    /**
     * The package author instance.
     *
     * @var \Metallizzer\Package\Author
     */
    protected $author;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:package {package_name? : Name of the package (<vendor>/<name>)}
        {--force : Create the package even if it already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('This command will guide you through creating your Laravel package.');

        $this->askPackageName();

        if ($git = exec('which git 2>/dev/null')) {
            $this->author->username = exec(escapeshellarg($git).' config user.name');
            $this->author->email    = exec(escapeshellarg($git).' config user.email');
        }

        if (!$this->option('force') && $this->package->exists()) {
            $this->error('The package already exists!');

            return false;
        }

        $this->askPackageDescription()
            ->askAuthorUsername()
            ->askPackageHomepage()
            ->askAuthorName()
            ->askAuthorEmail()
            ->askAuthorHomepage()
            ->askPackageNamespace();

        $this->package->create();

        $this->info(sprintf('Package %s created successfully.', $this->package->fullName));
    }

    protected function askPackageName()
    {
        $name = $this->argument('package_name');

        while (preg_match('/^[a-z0-9_\.\-]+\/[a-z0-9_\.\-]+$/', $name) !== 1) {
            $name = $this->ask('Package name (<vendor>/<name>)');
        }

        $this->package = new Package($name, $this->files);
        $this->author  = $this->package->author();

        return $this;
    }

    protected function askPackageDescription()
    {
        $this->package->description = $this->ask('Description');

        return $this;
    }

    protected function askPackageHomepage()
    {
        $this->package->homepage = $this->ask('Package homepage', $this->package->homepage);

        return $this;
    }

    protected function askAuthorUsername()
    {
        do {
            $username = $this->ask('Author github username', $this->author->username);
        } while (preg_match('/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/i', $username) !== 1);

        $this->author->username = $username;

        return $this;
    }

    protected function askAuthorName()
    {
        $this->author->name = $this->ask('Author name', $this->author->username);

        return $this;
    }

    protected function askAuthorEmail()
    {
        $this->author->email = $this->ask('Author email', $this->author->email);

        return $this;
    }

    protected function askAuthorHomepage()
    {
        $this->author->homepage = $this->ask('Author homepage', $this->author->homepage);

        return $this;
    }

    protected function askPackageNamespace()
    {
        $regex   = '/^([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)(\\\\(?1))*$/';
        $default = Str::studly($this->package->vendor).'\\'.Str::studly($this->package->name);

        while (preg_match($regex, $this->package->namespace) !== 1) {
            $this->package->namespace = $this->ask('Package namespace', $default);
        }

        return $this;
    }
}
