<?php declare(strict_types=1);

namespace Kcs\Serializer\Exclusion;

use Kcs\Serializer\Context;
use Kcs\Serializer\Metadata\ClassMetadata;
use Kcs\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for exclusion strategies.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ExclusionStrategyInterface
{
    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $metadata
     * @param Context       $context
     *
     * @return bool
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool;

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $property
     * @param Context          $context
     *
     * @return bool
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool;
}
