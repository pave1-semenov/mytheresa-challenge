<?php

namespace Mytheresa\Challenge\Utils;

use Symfony\Component\Filesystem\Exception\{IOException, IOExceptionInterface};
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Generic class which helps to read the content from json resource and deserialize it's content into specified object
 * @template T
 */
class JsonResourceReader
{
    public function __construct(
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
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new IOException("Failed to read file {$filePath}");
        }

        return $this->serializer->deserialize($content, $targetClass, 'json');
    }
}