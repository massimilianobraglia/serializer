<?php declare(strict_types=1);

namespace Kcs\Serializer\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\PHPCR\PersistentCollection as PHPCRPersistentCollection;
use Doctrine\ORM\PersistentCollection;
use Kcs\Serializer\Context;
use Kcs\Serializer\Direction;
use Kcs\Serializer\Type\Type;
use Kcs\Serializer\VisitorInterface;

class ArrayCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = [];
        $collectionTypes = [
            'ArrayCollection',
            ArrayCollection::class,
            PersistentCollection::class,
            MongoDBPersistentCollection::class,
            PHPCRPersistentCollection::class,
        ];

        foreach ($collectionTypes as $type) {
            $methods[] = [
                'direction' => Direction::DIRECTION_SERIALIZATION,
                'type' => $type,
                'method' => 'serializeCollection',
            ];

            $methods[] = [
                'direction' => Direction::DIRECTION_DESERIALIZATION,
                'type' => $type,
                'method' => 'deserializeCollection',
            ];
        }

        return $methods;
    }

    public function serializeCollection(VisitorInterface $visitor, Collection $collection, Type $type, Context $context)
    {
        return $visitor->visitArray($collection->toArray(), $type, $context);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, Type $type, Context $context)
    {
        return new ArrayCollection($visitor->visitArray($data, $type, $context));
    }
}
