<?php

namespace Gaufrette\Adapter\Metadata;

use Gaufrette\Core\Adapter\KnowsMetadata;

class MetadataAccessor implements KnowsMetadata
{
    const METHOD_PHP  = 'php';
    const METHOD_JSON = 'json';

    public function __construct($extension = 'metadata', $serialization = self::METHOD_PHP)
    {
        $this->extension     = $extension;
        $this->serialization = $serialization;
    }

    /**
     * {@inheritdoc}
     */
    public function readMetadata($path)
    {
        if (false === file_exists($this->getFilename($path))) {

            return array();
        }

        $content = file_get_contents($this->getFilename($path));

        switch ($this->serialization) {
            case self::METHOD_PHP:
                return unserialize($content);
            case self::METHOD_JSON:
                return json_decode($content);
            default:
                throw new \invalidargumentexception(sprintf('serialization method %s unknow', $this->method));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeMetadata($path, array $metadata)
    {
        $path = $this->getFilename($path);

        if (true === empty($metadata)) {
            if (true === file_exists($path)) {
                unlink($path);
            }

            return;
        }

        switch ($this->serialization) {
            case self::METHOD_PHP:
                $content = serialize($metadata);
                break;
            case self::METHOD_JSON:
                $content = json_encode($metadata);
                break;
            default:
                throw new \invalidargumentexception(sprintf('serialization method %s unknow', $this->method));
        }

        file_put_contents($path, $content);
    }

    private function getFilename($path)
    {
        return sprintf('%s.%s', $path, $this->extension);
    }
}
