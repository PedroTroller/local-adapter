<?php

namespace spec\Gaufrette\Adapter\Metadata;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MetadataAccessorSpec extends ObjectBehavior
{
    function letGo()
    {
        $file = sprintf('%s/file.txt', __DIR__);
        $meta = sprintf('%s.metadata', $file);

        if (file_exists($meta)) {
            unlink($meta);
        }
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Gaufrette\Adapter\Metadata\MetadataAccessor');
    }

    function it_creates_file_with_metadata()
    {
        $file = sprintf('%s/file.txt', __DIR__);
        $meta = sprintf('%s.metadata', $file);
        $data = array('test' => 'the_test', 1 => true);

        expect(file_exists($meta))->toBe(false);

        $this->writeMetadata($file, $data);

        expect(file_exists($meta))->toBe(true);
        expect(file_get_contents($meta))->toBe(serialize($data));
    }

    function it_doesnt_create_file_without_metadata()
    {
        $file = sprintf('%s/file.txt', __DIR__);
        $meta = sprintf('%s.metadata', $file);
        $data = array();

        expect(file_exists($meta))->toBe(false);

        $this->writeMetadata($file, $data);

        expect(file_exists($meta))->toBe(false);
    }

    function it_removes_file_when_no_metadata()
    {
        $file = sprintf('%s/file.txt', __DIR__);
        $meta = sprintf('%s.metadata', $file);
        $data = array('test' => 'the_test', 1 => true);

        expect(file_exists($meta))->toBe(false);

        $this->writeMetadata($file, $data);

        expect(file_exists($meta))->toBe(true);

        $this->writeMetadata($file, array());

        expect(file_exists($meta))->toBe(false);
    }

    function it_retrieve_data_from_path()
    {
        $file = sprintf('%s/file.txt', __DIR__);
        $meta = sprintf('%s.metadata', $file);
        $data = array('test' => 'the_test', 1 => true);

        $this->writeMetadata($file, $data);

        $this->readMetadata($file)->shouldReturn($data);
    }

    function it_doesnt_supports_unknown_serialisation_format()
    {
        $this->beConstructedWith('.metadata', 'test');
        $file = sprintf('%s/file.txt', __DIR__);
        $data = array('test');

        $this->shouldThrow(new \InvalidArgumentException('Serialization method test unknow'))->duringWriteMetadata($file, $data);
    }
}
