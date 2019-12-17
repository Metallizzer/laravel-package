<?php

namespace Metallizzer\Package;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Item
{
    protected $data = [];

    public function __get($key)
    {
        $key = Str::camel($key);

        if (method_exists($this, $key)) {
            return call_user_func([$this, $key]);
        }

        return Arr::get($this->data, $key);
    }

    public function __set($key, $value)
    {
        return Arr::set($this->data, $key, $value);
    }

    public function getReplacements()
    {
        $data = [];
        $base = Str::snake(class_basename($this));

        foreach ($this->data as $key => $value) {
            $data[":{$base}_{$key}"] = $value;
        }

        return $data;
    }
}
