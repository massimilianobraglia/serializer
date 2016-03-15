<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 * Modifications copyright (c) 2016 Alessandro Chitolina <alekitto@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Kcs\Serializer\Metadata;

use Kcs\Serializer\Annotation\ExclusionPolicy;
use Kcs\Serializer\Exception\InvalidArgumentException;
use Kcs\Metadata\ClassMetadata as BaseClassMetadata;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\MethodMetadata;

/**
 * Class Metadata used to customize the serialization process.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassMetadata extends BaseClassMetadata
{
    const ACCESSOR_ORDER_UNDEFINED = 'undefined';
    const ACCESSOR_ORDER_ALPHABETICAL = 'alphabetical';
    const ACCESSOR_ORDER_CUSTOM = 'custom';

    public $exclusionPolicy = ExclusionPolicy::NONE;
    public $defaultAccessType = PropertyMetadata::ACCESS_TYPE_PROPERTY;
    public $readOnly = false;

    /** @var MethodMetadata[] */
    public $preSerializeMethods = array();

    /** @var MethodMetadata[] */
    public $postSerializeMethods = array();

    /** @var MethodMetadata[] */
    public $postDeserializeMethods = array();

    public $xmlRootName;
    public $xmlRootNamespace;
    public $xmlNamespaces = array();
    public $accessorOrder;
    public $customOrder;
    public $handlerCallbacks = array();

    public $discriminatorDisabled = false;
    public $discriminatorBaseClass;
    public $discriminatorFieldName;
    public $discriminatorValue;
    public $discriminatorMap = array();

    public function setDiscriminator($fieldName, array $map)
    {
        if (empty($fieldName)) {
            throw new \InvalidArgumentException('The $fieldName cannot be empty.');
        }

        if (empty($map)) {
            throw new \InvalidArgumentException('The discriminator map cannot be empty.');
        }

        $this->discriminatorBaseClass = $this->getName();
        $this->discriminatorFieldName = $fieldName;
        $this->discriminatorMap = $map;
    }

    /**
     * Sets the order of properties in the class.
     *
     * @param string $order
     * @param array $customOrder
     *
     * @throws InvalidArgumentException When the accessor order is not valid
     * @throws InvalidArgumentException When the custom order is not valid
     */
    public function setAccessorOrder($order, array $customOrder = array())
    {
        if ( ! in_array($order, array(self::ACCESSOR_ORDER_UNDEFINED, self::ACCESSOR_ORDER_ALPHABETICAL, self::ACCESSOR_ORDER_CUSTOM), true)) {
            throw new InvalidArgumentException(sprintf('The accessor order "%s" is invalid.', $order));
        }

        foreach ($customOrder as $name) {
            if ( ! is_string($name)) {
                throw new InvalidArgumentException(sprintf('$customOrder is expected to be a list of strings, but got element of value %s.', json_encode($name)));
            }
        }

        $this->accessorOrder = $order;
        $this->customOrder = array_flip($customOrder);
        $this->sortProperties();
    }

    public function addAttributeMetadata(MetadataInterface $metadata)
    {
        parent::addAttributeMetadata($metadata);
        $this->sortProperties();
    }


    public function addPreSerializeMethod(MethodMetadata $method)
    {
        $this->preSerializeMethods[] = $method;
    }

    public function addPostSerializeMethod(MethodMetadata $method)
    {
        $this->postSerializeMethods[] = $method;
    }

    public function addPostDeserializeMethod(MethodMetadata $method)
    {
        $this->postDeserializeMethods[] = $method;
    }

    /**
     * @param integer $direction
     * @param string|integer $format
     * @param string $methodName
     */
    public function addHandlerCallback($direction, $format, $methodName)
    {
        $this->handlerCallbacks[$direction][$format] = $methodName;
    }

    public function merge(MetadataInterface $object)
    {
        if ( ! $object instanceof self) {
            throw new InvalidArgumentException('$object must be an instance of ClassMetadata.');
        }

        parent::merge($object);

        $this->preSerializeMethods = array_merge($object->preSerializeMethods, $this->preSerializeMethods);
        $this->postSerializeMethods = array_merge($object->postSerializeMethods, $this->postSerializeMethods);
        $this->postDeserializeMethods = array_merge($object->postDeserializeMethods, $this->postDeserializeMethods);
        $this->xmlRootName = $this->xmlRootName ?: $object->xmlRootName;
        $this->xmlRootNamespace = $this->xmlRootNamespace ?: $object->xmlRootNamespace;
        $this->xmlNamespaces = array_merge($object->xmlNamespaces, $this->xmlNamespaces);

        // Handler methods are not inherited

        if (! $this->accessorOrder && $object->accessorOrder) {
            $this->accessorOrder = $object->accessorOrder;
            $this->customOrder = $object->customOrder;
        }

        if ($this->discriminatorFieldName && $object->discriminatorFieldName &&
            $this->discriminatorFieldName !== $object->discriminatorFieldName) {
            throw new \LogicException(sprintf(
                'The discriminator of class "%s" would overwrite the discriminator of the parent class "%s". Please define all possible sub-classes in the discriminator of %s.',
                $this->getName(),
                $object->discriminatorBaseClass,
                $object->discriminatorBaseClass
            ));
        }

        if ($object->discriminatorMap && ! $this->getReflectionClass()->isAbstract()) {
            if (false === $typeValue = array_search($this->getName(), $object->discriminatorMap, true)) {
                throw new \LogicException(sprintf(
                    'The sub-class "%s" is not listed in the discriminator of the base class "%s".',
                    $this->getName(),
                    $this->discriminatorBaseClass
                ));
            }

            $this->discriminatorValue = $typeValue;
            $this->discriminatorFieldName = $object->discriminatorFieldName;

            $discriminatorProperty = new StaticPropertyMetadata(
                $this->getName(),
                $this->discriminatorFieldName,
                $typeValue
            );
            $discriminatorProperty->serializedName = $this->discriminatorFieldName;

            $this->addAttributeMetadata($discriminatorProperty);
        }

        $this->sortProperties();
    }

    public function registerNamespace($uri, $prefix = null)
    {
        if ( ! is_string($uri)) {
            throw new InvalidArgumentException(sprintf('$uri is expected to be a strings, but got value %s.', json_encode($uri)));
        }

        if ($prefix !== null) {
            if ( ! is_string($prefix)) {
                throw new InvalidArgumentException(sprintf('$prefix is expected to be a strings, but got value %s.', json_encode($prefix)));
            }
        } else {
            $prefix = "";
        }

        $this->xmlNamespaces[$prefix] = $uri;

    }

    public function __wakeup()
    {
        $this->sortProperties();
    }

    private function sortProperties()
    {
        switch ($this->accessorOrder) {
            case self::ACCESSOR_ORDER_ALPHABETICAL:
                ksort($this->attributesMetadata);
                break;

            case self::ACCESSOR_ORDER_CUSTOM:
                $order = $this->customOrder;
                uksort($this->attributesMetadata, function($a, $b) use ($order) {
                    $existsA = isset($order[$a]);
                    $existsB = isset($order[$b]);

                    if ( ! $existsA && ! $existsB) {
                        return 0;
                    }

                    if ( ! $existsA) {
                        return 1;
                    }

                    if ( ! $existsB) {
                        return -1;
                    }

                    return $order[$a] < $order[$b] ? -1 : 1;
                });
                break;
        }
    }
}