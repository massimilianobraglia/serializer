<?php declare(strict_types=1);
/*
 * Copyright 2017 Alessandro Chitolina <alekitto@gmail.com>
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

class AdditionalPropertyMetadata extends PropertyMetadata
{
    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;
        $this->readOnly = true;

        $this->setType($class.'::'.$name);
    }

    public function getValue($obj)
    {
        return $obj;
    }

    public function setValue($obj, $value)
    {
        throw new \LogicException('AdditionalPropertyMetadata is immutable.');
    }

    public function setAccessor($type, $getter = null, $setter = null)
    {
    }

    public function __wakeup()
    {
    }
}
