<?php

namespace Mytheresa\Challenge\Utils;

use Symfony\Component\Filesystem\Exception\{FileNotFoundException, IOException, IOExceptionInterface};
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Generic class which helps to read the content of json file and deserialize it's content into specified object
 * @template T
 */
class JsonFileReader
{
    public function __construct(
        private readonly Filesystem          $fs,
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * @param class-string<T> $targetClass
     * @return T
     * @throws SerializerException
     * @throws IOExceptionInterface
     */
    public function read(string $filePath, string $targetClass): object
    {
        if (!$this->fs->exists($filePath)) {
            throw new FileNotFoundException(path: $filePath);
        }
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new IOException("Failed to read file {$filePath}");
        }

        return $this->serializer->deserialize($content, $targetClass, 'json');
    }
}