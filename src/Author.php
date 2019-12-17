<?php

namespace Metallizzer\Package;

use Exception;

class Author extends Item
{
    protected $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function homepage()
    {
        if (array_key_exists('homepage', $this->data)) {
            return $this->data['homepage'];
        }

        if (!$this->username && !$this->package->vendor) {
            throw new Exception('Couldn\'t get default author homepage');
        }

        return 'https://github.com/'.$this->username ?: $this->package->vendor;
    }
}
