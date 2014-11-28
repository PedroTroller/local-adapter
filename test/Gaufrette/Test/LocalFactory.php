<?php

namespace Gaufrette\Test;

use Gaufrette\Adapter\Local;
use Gaufrette\TestSuite\Adapter\AdapterFactory;
use Phine\Path\Path;

class LocalFactory implements AdapterFactory
{
    private $pattern = '%s/../../files';

    public function create()
    {
        return new Local($this->getDirectory(), true);
    }

    public function destroy()
    {
        $dir = $this->getDirectory();

        if (false === is_dir($dir)) {

            return;
        }

        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file) {
            unlink(sprintf('%s/%s', $dir, $file));
        }

        rmdir($dir);
    }

    protected function getDirectory()
    {
        return Path::canonical(sprintf('%s/../../files', __DIR__));
    }
}
