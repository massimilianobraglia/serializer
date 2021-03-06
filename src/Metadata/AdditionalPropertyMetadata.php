<?php declare(strict_types=1);

namespace Kcs\Serializer\Metadata;

class AdditionalPropertyMetadata extends PropertyMetadata
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
        $this->readOnly = true;

        $this->setType($class.'::'.$name);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($obj)
    {
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($obj, $value): void
    {
        throw new \LogicException('AdditionalPropertyMetadata is immutable.');
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
    }
}
