<?php declare(strict_types=1);

namespace Kcs\Serializer;

use Kcs\Metadata\Factory\MetadataFactoryInterface;
use Kcs\Serializer\Construction\ObjectConstructorInterface;
use Kcs\Serializer\Exception\RuntimeException;
use Kcs\Serializer\Exception\UnsupportedFormatException;
use Kcs\Serializer\Handler\HandlerRegistryInterface;
use Kcs\Serializer\Type\Type;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Serializer implements SerializerInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $factory;

    /**
     * @var VisitorInterface[]
     */
    private $serializationVisitors;

    /**
     * @var VisitorInterface[]
     */
    private $deserializationVisitors;

    /**
     * @var GraphNavigator
     */
    private $navigator;

    /**
     * Constructor.
     *
     * @param MetadataFactoryInterface                $factory
     * @param Handler\HandlerRegistryInterface        $handlerRegistry
     * @param Construction\ObjectConstructorInterface $objectConstructor
     * @param VisitorInterface[]                      $serializationVisitors   of VisitorInterface
     * @param VisitorInterface[]                      $deserializationVisitors of VisitorInterface
     * @param EventDispatcherInterface                $dispatcher
     */
    public function __construct(
        MetadataFactoryInterface $factory,
        HandlerRegistryInterface $handlerRegistry,
        ObjectConstructorInterface $objectConstructor,
        array $serializationVisitors,
        array $deserializationVisitors,
        ?EventDispatcherInterface $dispatcher = null
    ) {
        $this->factory = $factory;
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;

        $this->navigator = new GraphNavigator($this->factory, $handlerRegistry, $objectConstructor, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?Type $type = null)
    {
        if (null === $context) {
            $context = new SerializationContext();
        }

        if (! isset($this->serializationVisitors[$format])) {
            throw new UnsupportedFormatException("The format \"$format\" is not supported for serialization");
        }

        return $this->visit($this->serializationVisitors[$format], $context, $data, $format, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, Type $type, string $format, ?DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = new DeserializationContext();
        }

        if (! isset($this->deserializationVisitors[$format])) {
            throw new UnsupportedFormatException("The format \"$format\" is not supported for deserialization");
        }

        return $this->visit($this->deserializationVisitors[$format], $context, $data, $format, $type);
    }

    /**
     * Converts objects to an array structure.
     *
     * This is useful when the data needs to be passed on to other methods which expect array data.
     *
     * @param mixed                $data    anything that converts to an array, typically an object or an array of objects
     * @param SerializationContext $context
     *
     * @return array
     */
    public function toArray($data, SerializationContext $context = null): array
    {
        $result = $this->serialize($data, 'array', $context);

        if (! is_array($result)) {
            throw new RuntimeException(sprintf(
                'The input data of type "%s" did not convert to an array, but got a result of type "%s".',
                is_object($data) ? get_class($data) : gettype($data),
                is_object($result) ? get_class($result) : gettype($result)
            ));
        }

        return $result;
    }

    /**
     * Restores objects from an array structure.
     *
     * @param array                  $data
     * @param Type                   $type
     * @param DeserializationContext $context
     *
     * @return mixed this returns whatever the passed type is, typically an object or an array of objects
     */
    public function fromArray(array $data, Type $type, DeserializationContext $context = null)
    {
        return $this->deserialize($data, $type, 'array', $context);
    }

    private function visit(VisitorInterface $visitor, Context $context, $data, $format, Type $type = null)
    {
        $data = $visitor->prepare($data);
        $context->initialize($format, $visitor, $this->navigator, $this->factory);

        $visitor->setNavigator($this->navigator);
        $this->navigator->accept($data, $type, $context);

        return $visitor->getResult();
    }

    /**
     * @return MetadataFactoryInterface
     */
    public function getMetadataFactory(): MetadataFactoryInterface
    {
        return $this->factory;
    }
}
