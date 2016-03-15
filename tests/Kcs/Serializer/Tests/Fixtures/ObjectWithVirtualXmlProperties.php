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

use Kcs\Serializer\Annotation\Groups;
use Kcs\Serializer\Annotation\XmlValue;
use Kcs\Serializer\Annotation\XmlAttribute;
use Kcs\Serializer\Annotation\XmlList;
use Kcs\Serializer\Annotation\XmlMap;
use Kcs\Serializer\Annotation\Since;
use Kcs\Serializer\Annotation\Until;
use Kcs\Serializer\Annotation\VirtualProperty;
use Kcs\Serializer\Annotation\SerializedName;

class ObjectWithVirtualXmlProperties
{

    /**
     *
     * @VirtualProperty
     * @SerializedName("foo")
     * @Groups({"attributes"})
     * @XmlAttribute
     */
    public function getVirualXmlAttributeValue()
    {
        return 'bar';
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("xml-value")
     * @Groups({"values"})
     * @XmlValue
     */
    public function getVirualXmlValue()
    {
        return 'xml-value';
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("list")
     * @Groups({"list"})
     * @XmlList(inline = true, entry = "val")
     */
    public function getVirualXmlList()
    {
        return array('One','Two');
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("map")
     * @Groups({"map"})
     * @XmlMap(keyAttribute = "key")
     */
    public function getVirualXmlMap()
    {
        return array(
            'key-one'   => 'One',
            'key-two'   => 'Two'
        );
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("low")
     * @Groups({"versions"})
     * @Until("8")
     */
    public function getVirualLowValue()
    {
        return 1;
    }

    /**
     * @VirtualProperty
     * @SerializedName("hight")
     * @Groups({"versions"})
     * @Since("8")
     */
    public function getVirualHighValue()
    {
        return 8;
    }

}