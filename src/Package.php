<?php

namespace Metallizzer\Package;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Metallizzer\Package\Exceptions\PackageException;
use RuntimeException;

class Package extends Item
{
    protected $files;
    protected $author;

    public function __construct($name, Filesystem $files = null)
    {
        if (!preg_match('/^[a-z0-9_\.\-]+\/[a-z0-9_\.\-]+$/', $name)) {
            throw new PackageException(sprintf('The package name %s is invalid, it should be lowercase and have a vendor name, a forward slash, and a package name, matching: [a-z0-9_\.\-]+/[a-z0-9_\.\-]+', $name));
        }

        if (null === $files) {
            $files = app('files');
        }

        $this->author = new Author($this);
        $this->files  = $files;

        list($this->vendor, $this->name) = explode('/', $name);
    }

    public function __get($key)
    {
        if (strpos($key, 'author') !== 0) {
            $key = Str::camel($key);

            if (method_exists($this, $key)) {
                return call_user_func([$this, $key]);
            }

            return Arr::get($this->data, $key);
        }

        $key = Str::camel(Str::after($key, 'author'));

        return $key ? $this->author->{$key} : $this->author;
    }

    public function __set($key, $value)
    {
        if (strpos($key, 'author') !== 0) {
            return Arr::set($this->data, Str::camel($key), $value);
        }

        $key = Str::camel(Str::after($key, 'author'));

        return $this->author->{$key} = $value;
    }

    public function path()
    {
        return config('package.path').'/'.$this->fullName();
    }

    public function author()
    {
        return $this->author;
    }

    public function fullName()
    {
        if (!$this->vendor || !$this->name) {
            throw new RuntimeException('Couldn\'t get package path');
        }

        return $this->vendor.'/'.$this->name;
    }

    public function exists()
    {
        return $this->files->exists($this->path());
    }

    public function homepage()
    {
        if (array_key_exists('homepage', $this->data)) {
            return $this->data['homepage'];
        }

        if (!$this->vendor || !$this->name) {
            throw new RuntimeException('Couldn\'t get default package homepage');
        }

        return 'https://github.com/'.($this->author->username ?: $this->vendor).'/'.$this->name;
    }

    public function create()
    {
        $last    = Str::afterLast($this->namespace, '\\');
        $replace = array_merge(
            $this->getReplacements(),
            $this->author->getReplacements(),
            [
                ':php_version'       => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                ':vendor'            => $this->vendor,
                ':namespace'         => $this->namespace,
                ':today'             => date('Y-m-d'),
                ':year'              => date('Y'),
                'DummyNamespace'     => $this->namespace,
                'Dummy\\\\Namespace' => str_replace('\\', '\\\\', $this->namespace),
                'DummyClass'         => Str::studly($last),
                'dummyVar'           => Str::camel($last),
                'dummy_name'         => Str::snake(Str::camel($last)),
            ]
        );

        $this->files->copyDirectory(__DIR__.'/../stubs', $this->path);

        foreach ($this->files->allFiles($this->path, true) as $file) {
            $replacement = $replace;

            if ($file->getExtension() === 'json') {
                $replacement = array_map(function ($v) {
                    return substr(json_encode((string) $v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
                }, $replace);
            }

            $this->files->replace(
                $file->getRealPath(),
                strtr($this->files->get($file->getRealPath()), $replacement)
            );

            $filename = strtr($file->getRealPath(), $replace);

            if ($file->getExtension() === 'stub') {
                $filename = substr($filename, 0, -5);
            }

            if ($filename !== $file->getRealPath()) {
                $this->files->move($file->getRealpath(), $filename);
            }
        }
    }

    public function getNamespace()
    {
        if (!is_null($this->namespace)) {
            return $this->namespace;
        }

        $path     = $this->path.'/composer.json';
        $composer = json_decode($this->files->get($path), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath($this->path.'/src')
                    === realpath($this->path.'/'.$pathChoice)
                ) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect package namespace.');
    }

    public function checkAutoload($add = false)
    {
        $loader = require base_path('vendor/autoload.php');

        if (array_key_exists($this->getNamespace(), $loader->getPrefixesPsr4())) {
            return true;
        } elseif (!$add) {
            return false;
        }

        $loader->addPsr4($this->getNamespace(), $this->path.'/src');

        return true;
    }
}
