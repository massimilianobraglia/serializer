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

namespace Kcs\Serializer\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Kcs\Serializer\Annotation\AccessType;
use Kcs\Serializer\Annotation\Type;
use Kcs\Serializer\Annotation\PostDeserialize;

/**
 * @AccessType("property")
 */
class CircularReferenceParent
{
    /** @Type("array<Kcs\Serializer\Tests\Fixtures\CircularReferenceChild>") */
    protected $collection = array();

    /** @Type("ArrayCollection<Kcs\Serializer\Tests\Fixtures\CircularReferenceChild>") */
    private $anotherCollection;

    public function __construct()
    {
        $this->collection[] = new CircularReferenceChild('child1', $this);
        $this->collection[] = new CircularReferenceChild('child2', $this);

        $this->anotherCollection = new ArrayCollection();
        $this->anotherCollection->add(new CircularReferenceChild('child1', $this));
        $this->anotherCollection->add(new CircularReferenceChild('child2', $this));
    }

    /** @PostDeserialize */
    public function afterDeserialization()
    {
        if (!$this->collection) {
            $this->collection = array();
        }
        foreach ($this->collection as $v) {
            $v->setParent($this);
        }

        if (!$this->anotherCollection) {
            $this->anotherCollection = new ArrayCollection();
        }
        foreach ($this->anotherCollection as $v) {
            $v->setParent($this);
        }
    }
}
