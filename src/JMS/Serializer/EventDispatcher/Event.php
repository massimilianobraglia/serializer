<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;

class Event
{
    protected $type;
    private $context;
    private $data;

    public function __construct(Context $context, $data, array $type)
    {
        $this->context = $context;
        $this->type = $type;
        $this->data = $data;
    }

    public function getVisitor()
    {
        return $this->context->getVisitor();
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($name, array $params = array())
    {
        $this->type = array('name' => $name, 'params' => $params);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
