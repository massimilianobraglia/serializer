<?php declare(strict_types=1);

namespace Kcs\Serializer\Metadata\Loader\Processor;

use Kcs\Metadata\MetadataInterface;
use Kcs\Serializer\Metadata\ClassMetadata;

class AccessTypeProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function process($annotation, MetadataInterface $metadata): void
    {
        if ($metadata instanceof ClassMetadata) {
            $metadata->defaultAccessType = $annotation->type;
        }
    }
}
