<?php

namespace Metallizzer\Package\Tests;

use Metallizzer\Package\Author;
use Metallizzer\Package\Exceptions\PackageException;
use Metallizzer\Package\Package;

class PackageTest extends TestCase
{
    protected $package;
    protected $author;

    public function setUp(): void
    {
        parent::setUp();

        $this->package = new Package('vendor/package');
        $this->author  = $this->package->author;

        $this->package->namespace   = 'Vendor\\Package';
        $this->package->description = 'Package description';

        $this->author->username = 'Vendor';
        $this->author->name     = $this->author->username;
        $this->author->email    = 'Vendor@users.noreply.github.com';
    }

    public function testPackageException()
    {
        $this->expectException(PackageException::class);

        new Package('wrong_package_name');
    }

    public function testAuthor()
    {
        $this->assertInstanceOf(Author::class, $this->package->author());
    }

    public function testPath()
    {
        $this->assertSame($this->tempPath('vendor/package'), $this->package->path);
    }

    public function testHomepage()
    {
        $this->assertSame('https://github.com/Vendor/package', $this->package->homepage);
    }

    public function testAuthorHomepage()
    {
        $this->assertSame('https://github.com/Vendor', $this->author->homepage);
    }

    public function testFullName()
    {
        $this->assertSame('vendor/package', $this->package->fullName);
    }

    public function testNotExists()
    {
        $this->assertFalse($this->package->exists);
    }

    public function testNamespace()
    {
        $this->package->create();

        $this->assertSame('Vendor\\Package', $this->package->getNamespace());
    }
}
