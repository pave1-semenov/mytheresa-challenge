<?php

namespace Mytheresa\Challenge\API\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class RequestAttributesExtractor
{
    public function __construct(private readonly DecoderInterface $decoder)
    {
    }

    public function extractFromQuery(Request $request): array
    {
        return $request->query->all();
    }

    public function extractFromBody(Request $request): array
    {
        $body = $request->getContent();
        $format = $request->getContentType();

        return $body !== "" ? $this->decoder->decode($body, $format) : [];
    }
}