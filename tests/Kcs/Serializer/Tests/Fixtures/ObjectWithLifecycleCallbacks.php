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

use Kcs\Serializer\Annotation\Exclude;
use Kcs\Serializer\Annotation\PreSerialize;
use Kcs\Serializer\Annotation\PostSerialize;
use Kcs\Serializer\Annotation\PostDeserialize;
use Kcs\Serializer\Annotation\Type;

class ObjectWithLifecycleCallbacks
{
    /**
     * @Exclude
     */
    private $firstname;

    /**
     * @Exclude
     */
    private $lastname;

    /**
     * @Type("string")
     */
    private $name;

    public function __construct($firstname = 'Foo', $lastname = 'Bar')
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * @PreSerialize
     */
    public function prepareForSerialization()
    {
        $this->name = $this->firstname.' '.$this->lastname;
    }

    /**
     * @PostSerialize
     */
    public function cleanUpAfterSerialization()
    {
        $this->name = null;
    }

    /**
     * @PostDeserialize
     */
    public function afterDeserialization()
    {
        list($this->firstname, $this->lastname) = explode(' ', $this->name);
        $this->name = null;
    }
}