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
        $key = base64_decode($key);

        return parent::getFullPath($key);
    }
}
