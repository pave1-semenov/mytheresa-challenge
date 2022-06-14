<?php

namespace Mytheresa\Challenge\Persistence\Entity\Field;

use Doctrine\ORM\Mapping\{Column, CustomIdGenerator, GeneratedValue, Id};
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Generic traits which helps to automatically fill UUID Primary Keys on entity creation
 */
trait UuidPkTrait
{
    #[Id, Column(type: "uuid", unique: true), GeneratedValue(strategy: "CUSTOM"), CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }
}