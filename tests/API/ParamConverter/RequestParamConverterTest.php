<?php

namespace Mytheresa\Challenge\Tests\API\ParamConverter;

use Mockery\MockInterface;
use Mytheresa\Challenge\API\ParamConverter\RequestAttributesExtractor;
use Mytheresa\Challenge\API\ParamConverter\RequestData;
use Mytheresa\Challenge\API\ParamConverter\RequestParamConverter;
use Mytheresa\Challenge\API\ParamConverter\RequestType;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestParamConverterTest extends TestCase
{
    private RequestAttributesExtractor|MockInterface $extractor;
    private DenormalizerInterface|MockInterface $denormalizer;
    private ValidatorInterface|MockInterface $validator;
    private RequestParamConverter $converter;

    /**
     * @dataProvider provideSupportsArguments
     */
    public function testSupports(string $class, bool $expected): void
    {
        $conf = \Mockery::mock(ParamConverter::class, ['getClass' => $class]);

        $actual = $this->converter->supports($conf);

        self::assertEquals($actual, $expected);
    }

    public function provideSupportsArguments(): iterable
    {
        $supportedClass = new #[RequestData(RequestType::QUERY)] class {
        };
        yield [$supportedClass::class, true];
        $notSupportedClass = new class {
        };
        yield [$notSupportedClass::class, false];
    }

    /**
     * @dataProvider provideApplyArguments
     */
    public function testApply(object $model, RequestType $type, bool $validate, int $violationsCount)
    {
        $name = 'test';
        $conf = \Mockery::mock(ParamConverter::class, ['getClass' => $model::class, 'getName' => $name]);
        $request = \Mockery::mock(Request::class);
        $request->attributes = \Mockery::mock(ParameterBag::class);
        $attributes = ['someKey' => 'someValue'];
        $this->extractor->shouldReceive($type == RequestType::QUERY ? 'extractFromQuery' : 'extractFromBody')->with($request)->andReturn($attributes);
        $this->denormalizer->shouldReceive('denormalize')->withSomeOfArgs($attributes, $model::class)->andReturn($model);
        $exceptional = false;
        if ($validate) {
            $violations = \Mockery::mock(ConstraintViolationListInterface::class, ['count' => $violationsCount]);
            $this->validator->shouldReceive('validate')->with($model)->andReturn($violations);
            if ($violationsCount > 0) {
                self::expectException(UnprocessableEntityHttpException::class);
                $exceptional = true;
            }
        }
        if (!$exceptional) {
            $request->attributes->shouldReceive('set')->with($name, $model)->once();
        }

        $this->converter->apply($request, $conf);

        self::assertTrue(true);
    }

    public function provideApplyArguments(): iterable
    {
        $class = new #[RequestData(type: RequestType::QUERY, validate: true)] class {
        };
        yield [new $class, RequestType::QUERY, true, 0];
        $class = new #[RequestData(type: RequestType::BODY, validate: true)] class {
        };
        yield [new $class, RequestType::BODY, true, 0];
        $class = new #[RequestData(type: RequestType::QUERY, validate: false)] class {
        };
        yield [new $class, RequestType::QUERY, false, 0];
        $class = new #[RequestData(type: RequestType::BODY, validate: true)] class {
        };
        yield [new $class, RequestType::BODY, true, 10];
    }

    protected function setUp(): void
    {
        $this->extractor = \Mockery::mock(RequestAttributesExtractor::class);
        $this->denormalizer = \Mockery::mock(DenormalizerInterface::class);
        $this->validator = \Mockery::mock(ValidatorInterface::class);
        $this->converter = new RequestParamConverter($this->extractor, $this->denormalizer, $this->validator);
    }
}
