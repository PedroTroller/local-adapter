<?php

namespace Gaufrette\Adapter\Local;

use Gaufrette\Core\Adapter\Configuration as ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return array('directory');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionalOptions()
    {
        return array('create', 'mode');
    }
}
