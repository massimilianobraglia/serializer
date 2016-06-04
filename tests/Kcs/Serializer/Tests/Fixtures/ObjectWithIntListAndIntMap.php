<?php

namespace Kcs\Serializer\Tests\Fixtures;

use Kcs\Serializer\Annotation as Serializer;

class ObjectWithIntListAndIntMap
{
    /** @Serializer\Type("array<integer>") @Serializer\XmlList */
    private $list;

    /** @Serializer\Type("array<integer,integer>") @Serializer\XmlMap */
    private $map;

    public function __construct(array $list, array $map)
    {
        $this->list = $list;
        $this->map = $map;
    }
}
