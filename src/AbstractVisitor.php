<?php declare(strict_types=1);

namespace Kcs\Serializer;

use Kcs\Serializer\Naming\PropertyNamingStrategyInterface;
use Kcs\Serializer\Type\Type;

abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * @var PropertyNamingStrategyInterface
     */
    protected $namingStrategy;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
        $this->setNavigator(null);
    }

    public function getNamingStrategy(): PropertyNamingStrategyInterface
    {
        return $this->namingStrategy;
    }

    public function prepare($data)
    {
        return $data;
    }

    public function visitCustom(callable $handler, $data, Type $type, Context $context)
    {
        return $handler($this, $data, $type, $context);
    }

    protected function getElementType(Type $type)
    {
        if (0 === $type->countParams()) {
            return null;
        }

        $params = $type->getParams();
        if (isset($params[1]) && $params[1] instanceof Type) {
            return $params[1];
        }

        return $params[0];
    }
}
