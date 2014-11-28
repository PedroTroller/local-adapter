<?php

namespace Gaufrette\Adapter;

use Gaufrette\Adapter\Metadata\MetadataAccessor;
use Gaufrette\Core\Adapter;
use Gaufrette\Core\Adapter\KnowsContent;
use Gaufrette\Core\Adapter\KnowsMetadata;
use Gaufrette\Core\Adapter\KnowsMimeType;
use Gaufrette\Core\Adapter\KnowsSize;
use Phine\Path\Path;

class Local implements Adapter, KnowsContent, KnowsMimeType, KnowsSize, KnowsMetadata
{
    /**
     * @var string $directory
     */
    private $directory;

    /**
     * @var boolean $create
     */
    private $create;

    /**
     * @var int $mode
     */
    private $mode;

    /**
     * @var MetadataAccessor $metadataAccessor
     */
    private $metadataAccessor;

    /**
     * @param string $directory
     * @param boolean $create
     * @param int $mode
     *
     * @return void
     */
    public function __construct($directory, $create = false, $mode = 0777)
    {
        $this->directory        = Path::canonical($directory);
        $this->create           = $create;
        $this->mode             = $mode;
        $this->metadataAccessor = new MetadataAccessor();
    }

    /**
     * @param MetadataAccessor $metadataAccessor if null, metadata feature will be disabled
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
        return true === file_exists($this->getFullPath($path));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getFullPath($key)
    {
        $fullpath = sprintf('%s/%s', $this->getDirectory(), $key);

        return Path::canonical($fullpath);
    }

    private function getDirectory()
    {
        $this->ensureDirectoryExists($this->directory);

        if (is_link($this->directory)) {
            $this->directory = realpath($this->directory);
        }

        return $this->directory;
    }

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

