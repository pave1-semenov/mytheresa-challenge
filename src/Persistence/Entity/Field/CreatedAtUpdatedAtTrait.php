<?php

namespace Mytheresa\Challenge\Persistence\Entity\Field;

/**
 * Trait combines CreatedAtTrain and UpdatedAtField behaviour
 */
trait CreatedAtUpdatedAtTrait
{
    use CreatedAtTrait, UpdatedAtTrait;
}