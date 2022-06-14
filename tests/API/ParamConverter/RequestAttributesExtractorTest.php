<?php

namespace Mytheresa\Challenge\Tests\API\ParamConverter;

use Mockery\MockInterface;
use Mytheresa\Challenge\API\ParamConverter\RequestAttributesExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class RequestAttributesExtractorTest extends TestCase
{
    private DecoderInterface|MockInterface $decoder;
    private RequestAttributesExtractor $extractor;


    public function testExtractFromQuery(): void
    {
        $params = ['key' => 'value'];
        $request = \Mockery::mock(Request::class);
        $request->query = \Mockery::mock(ParameterBag::class, ['all' => $params]);

        $actual = $this->extractor->extractFromQuery($request);

        self::assertEquals($params, $actual);
    }

    public function testExtractFromBody(): void
    {
        $content = '{"some":"json"}';
        $contentType = 'json';
        $request = \Mockery::mock(Request::class, ['getContent' => $content, 'getContentType' => $contentType]);
        $expected = ['some' => 'json'];
        $this->decoder->shouldReceive('decode')->with($content, $contentType)->andReturn($expected);

        $actual = $this->extractor->extractFromBody($request);

        self::assertEquals($expected, $actual);
    }

    public function testExtractFromEmptyBody(): void
    {
        $content = '';
        $contentType = 'json';
        $request = \Mockery::mock(Request::class, ['getContent' => $content, 'getContentType' => $contentType]);
        $expected = [];
        $this->decoder->shouldNotReceive('decode');

        $actual = $this->extractor->extractFromBody($request);

        self::assertEquals($expected, $actual);
    }

    protected function setUp(): void
    {
        $this->decoder = \Mockery::mock(DecoderInterface::class);
        $this->extractor = new RequestAttributesExtractor($this->decoder);
    }
}
