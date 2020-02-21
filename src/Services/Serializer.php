<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct()
    {
        $this->serializer = new SymfonySerializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function serializeModel($model, string $format = 'json')
    {
        return $this->serializer->serialize($model, $format);
    }

    public function deserializeModel($data, $model, $objectToPopulate = null, string $format = 'json')
    {
        $options = [];

        if ($objectToPopulate != null) {
            $options['object_to_populate'] = $objectToPopulate;
        }

        return $this->serializer->deserialize($data, $model, $format, $options);
    }

}