<?php

namespace Henzeb\Query\Tests\Helpers;

use Error;

trait Errors
{
    public function expectError(): void
    {
        $this->expectException(Error::class);
    }
}
