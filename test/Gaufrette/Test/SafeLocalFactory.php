<?php

namespace Gaufrette\Test;

use Gaufrette\Adapter\SafeLocal;
use Gaufrette\Test\LocalFactory;

class SafeLocalFactory extends LocalFactory
{
    public function create()
    {
        return new SafeLocal($this->getDirectory(), true);
    }
}
