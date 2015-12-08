<?php

namespace Gaufrette\Adapter;

use Gaufrette\Adapter\Metadata\MetadataAccessor;
use Gaufrette\Core\Adapter;
use Gaufrette\Core\Adapter\CanListKeys;
use Gaufrette\Core\Adapter\HasConfiguration;
use Gaufrette\Core\Adapter\KnowsContent;
use Gaufrette\Core\Adapter\KnowsLastAccess;
use Gaufrette\Core\Adapter\KnowsLastModification;
use Gaufrette\Core\Adapter\KnowsMetadata;
use Gaufrette\Core\Adapter\KnowsMimeType;
use Gaufrette\Core\Adapter\KnowsSize;
use Phine\Path\Path;

class Local implements
    Adapter,
    CanListKeys,
    HasConfiguration,
    KnowsContent,
    KnowsLastAccess,
    KnowsLastModification,
    KnowsMetadata,
    KnowsMimeType,
    KnowsSize
{
    /**
     * @type string
     */
    private $directory;

    /**
     * @type bool
     */
    private $create;

    /**
     * @type int
     */
    private $mode;

    /**
     * @type MetadataAccessor
     */
    private $metadataAccessor;

    /**
     * @param string $directory
     * @param bool   $create
     * @param int    $mode
     */
    public function __construct($directory, $create = false, $mode = 0777)
    {
        $this->directory        = Path::canonical($directory);
        $this->create           = $create;
        $this->mode             = $mode;
        $this->metadataAccessor = new MetadataAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromConfiguration(array $configuration)
    {
        $configuration = array_merge(
            array('create' => false, 'mode' => 0777),
            $configuration
        );

        return new self($configuration['directory'], $configuration['create'], $configuration['mode']);
    }

    /**
     * @param MetadataAccessor|null $metadataAccessor if null, metadata feature will be disabled
     *
     * @return
     */
    public function setMetadataAccessor(MetadataAccessor $metadataAccessor = null)
    {
        $this->metadataAccessor = $metadataAccessor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function readContent($key)
    {
        return file_get_contents($this->getFullPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function writeContent($key, $content)
    {
        file_put_contents($this->getFullPath($key), $content);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function readMetadata($key)
    {
        if (null !== $this->metadataAccessor) {
            return $this->metadataAccessor->readMetadata($this->getFullPath($key));
        }

        return array();
    }

    public function writeMetadata($key, array $metadata)
    {
        if (null !== $this->metadataAccessor) {
            $this->metadataAccessor->writeMetadata($this->getFullPath($key), $metadata);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function readMimeType($key)
    {
        $info = new \finfo(FILEINFO_MIME_TYPE);

        return $info->buffer($this->readContent($key));
    }

    /**
     * {@inheritdoc}
     */
    public function readSize($key)
    {
        return filesize($this->getFullPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function readLastModification($key)
    {
        return filemtime($this->getFullPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function writeLastModification($key, $time)
    {
        touch($this->getFullPath($key), $time);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function readLastAccess($key)
    {
        return fileatime($this->getFullPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function writeLastAccess($key, $time)
    {
        touch($this->getFullPath($key), $this->readLastModification($key), $time);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function listKeys($prefix = '')
    {
        $files     = array();
        $directory = new \DirectoryIterator($this->getDirectory());

        foreach ($directory as $file) {
            if (true === $file->isFile()) {
                $files[] = $file->getFilename();
            }
        }
        sort($files);

        if ('' !== $prefix) {
            $files = array_filter($files, function ($e) use ($prefix) { return 0 === strpos($e, $prefix); });
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unlink($this->getFullPath($key));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return true === file_exists($this->getFullPath($key));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getFullPath($key)
    {
        $fullpath = sprintf('%s/%s', $this->getDirectory(), $key);

        return Path::canonical($fullpath);
    }

    /**
     * @return string
     */
    private function getDirectory()
    {
        $this->ensureDirectoryExists($this->directory);

        if (true === is_link($this->directory)) {
            $this->directory = realpath($this->directory);
        }

        return $this->directory;
    }

    /**
     * @param string $directory
     */
    private function ensureDirectoryExists($directory)
    {
        if (true === is_dir($directory)) {
            return;
        }

        if (false === $this->create) {
            throw new \RuntimeException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $created = mkdir($directory, $this->mode, true);

        if (false === $created && false === is_dir($directory)) {
            throw new \RuntimeException(sprintf('The directory \'%s\' could not be created.', $directory));
        }
    }
}
