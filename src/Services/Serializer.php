<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Serializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct()
    {
        $classMetaDataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $objectNormalizer = new ObjectNormalizer($classMetaDataFactory, null, null, new PhpDocExtractor());
        $this->serializer = new SymfonySerializer([
            new ArrayDenormalizer(),
            $objectNormalizer,
        ], [
            new JsonEncoder(),
        ]);
    }

    public function serializeModel($model, string $format = 'json')
    {
        return $this->serializer->serialize($model, $format);
    }

    public function deserializeModel($data, $model, $objectToPopulate = null, bool $multiple = false, string $format = 'json')
    {
        if ($data == null) {
            return null;
        }

        $options = [];

        if ($objectToPopulate != null) {
            $options['object_to_populate'] = $objectToPopulate;
        }

        if ($multiple === true) {
            $result = [];

            foreach ($data as $item) {
                $result[] = $this->serializer->deserialize(json_encode($item), $model, $format, $options);
            }

            return $result;
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        return $this->serializer->deserialize($data, $model, $format, $options);
    }

    public function deserializeMultipleModel($data, $model, string $format = 'json')
    {
        return $this->deserializeModel($data, $model, null, true, $format);
    }

}