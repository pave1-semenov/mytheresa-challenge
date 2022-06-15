<?php

namespace Mytheresa\Challenge\API\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This class helps to automatically parse request data (either in query or body) into specified object
 * And inject it as controller's action argument
 */
class RequestParamConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly RequestAttributesExtractor $attributesExtractor,
        private readonly DenormalizerInterface      $denormalizer,
        private readonly ValidatorInterface         $validator
    )
    {

    }

    /**
     * @throws BadRequestHttpException|UnprocessableEntityHttpException
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        try {
            /** supported() should've been called before this method, so we're absolutely sure that
             * target class contains the needed attribute
             *
             * @var RequestData $attribute
             */
            $attribute = (new \ReflectionClass($configuration->getClass()))->getAttributes(RequestData::class)[0]->newInstance();
            $requestAttributes = match ($attribute->getType()) {
                RequestType::BODY => $this->attributesExtractor->extractFromBody($request),
                RequestType::QUERY => $this->attributesExtractor->extractFromQuery($request)
            };

            $requestModel = $this->denormalizer->denormalize($requestAttributes, $configuration->getClass(), context: [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]);
            if ($attribute->shouldValidate()) {
                $violations = $this->validator->validate($requestModel);
                if ($violations->count() > 0) {
                    /**
                     * This exception could be replaced by some custom one to expose information
                     * About violations
                     */
                    throw new UnprocessableEntityHttpException("Request validation failed");
                }
            }

            $request->attributes->set($configuration->getName(), $requestModel);
        } catch (SerializerException|\ReflectionException $e) {
            /**
             * Here we capture possible deserialization exceptions in case
             * Arguments were passed with a wrong type.
             * For example: string when int was expected
             */
            throw new BadRequestHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * Only classes which have a RequestData are supported
     */
    public function supports(ParamConverter $configuration): bool
    {
        $reflection = new \ReflectionClass($configuration->getClass());
        $attributes = $reflection->getAttributes(RequestData::class);

        return count($attributes) > 0;
    }

}