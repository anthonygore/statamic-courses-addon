<?php

namespace Anthonygore\Courses\Tests;

use Anthonygore\Courses\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
