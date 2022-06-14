<?php

namespace Mytheresa\Challenge\API\ParamConverter;

use Attribute;

/**
 * This attribute marks the class to be processed by RequestParamConverter
 */
#[Attribute(Attribute::TARGET_CLASS)]
class RequestData
{
    public function __construct(private readonly RequestType $type, private readonly bool $validate = true)
    {
    }

    public function getType(): RequestType
    {
        return $this->type;
    }

    public function shouldValidate(): bool
    {
        return $this->validate;
    }
}