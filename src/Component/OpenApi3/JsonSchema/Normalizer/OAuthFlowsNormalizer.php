<?php

declare(strict_types=1);

/*
 * This file has been auto generated by Jane,
 *
 * Do no edit it directly.
 */

namespace Jane\Component\OpenApi3\JsonSchema\Normalizer;

use Jane\Component\JsonSchemaRuntime\Reference;
use Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OAuthFlowsNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuthFlows';
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof \Jane\Component\OpenApi3\JsonSchema\Model\OAuthFlows;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \Jane\Component\OpenApi3\JsonSchema\Model\OAuthFlows();
        if (\array_key_exists('implicit', $data) && $data['implicit'] !== null) {
            $object->setImplicit($this->denormalizer->denormalize($data['implicit'], 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ImplicitOAuthFlow', 'json', $context));
            unset($data['implicit']);
        } elseif (\array_key_exists('implicit', $data) && $data['implicit'] === null) {
            $object->setImplicit(null);
        }
        if (\array_key_exists('password', $data) && $data['password'] !== null) {
            $object->setPassword($this->denormalizer->denormalize($data['password'], 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PasswordOAuthFlow', 'json', $context));
            unset($data['password']);
        } elseif (\array_key_exists('password', $data) && $data['password'] === null) {
            $object->setPassword(null);
        }
        if (\array_key_exists('clientCredentials', $data) && $data['clientCredentials'] !== null) {
            $object->setClientCredentials($this->denormalizer->denormalize($data['clientCredentials'], 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ClientCredentialsFlow', 'json', $context));
            unset($data['clientCredentials']);
        } elseif (\array_key_exists('clientCredentials', $data) && $data['clientCredentials'] === null) {
            $object->setClientCredentials(null);
        }
        if (\array_key_exists('authorizationCode', $data) && $data['authorizationCode'] !== null) {
            $object->setAuthorizationCode($this->denormalizer->denormalize($data['authorizationCode'], 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\AuthorizationCodeOAuthFlow', 'json', $context));
            unset($data['authorizationCode']);
        } elseif (\array_key_exists('authorizationCode', $data) && $data['authorizationCode'] === null) {
            $object->setAuthorizationCode(null);
        }
        foreach ($data as $key => $value) {
            if (preg_match('/^x-/', (string) $key)) {
                $object[$key] = $value;
            }
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = [];
        if (null !== $object->getImplicit()) {
            $data['implicit'] = $this->normalizer->normalize($object->getImplicit(), 'json', $context);
        } else {
            $data['implicit'] = null;
        }
        if (null !== $object->getPassword()) {
            $data['password'] = $this->normalizer->normalize($object->getPassword(), 'json', $context);
        } else {
            $data['password'] = null;
        }
        if (null !== $object->getClientCredentials()) {
            $data['clientCredentials'] = $this->normalizer->normalize($object->getClientCredentials(), 'json', $context);
        } else {
            $data['clientCredentials'] = null;
        }
        if (null !== $object->getAuthorizationCode()) {
            $data['authorizationCode'] = $this->normalizer->normalize($object->getAuthorizationCode(), 'json', $context);
        } else {
            $data['authorizationCode'] = null;
        }
        foreach ($object as $key => $value) {
            if (preg_match('/^x-/', (string) $key)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
