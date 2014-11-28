<?php

namespace Gaufrette\Adapter;

use Gaufrette\Adapter\Local;

class SafeLocal extends Local
{
    /**
     * {@inheritdoc}
     */
    protected function getFullPath($key)
    {
        $key = base64_encode($key);

        return parent::getFullPath($key);
    }
}
