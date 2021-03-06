<?php declare(strict_types=1);

namespace Kcs\Serializer;

use Symfony\Component\Yaml\Yaml;

class YamlDeserializationVisitor extends GenericDeserializationVisitor
{
    /**
     * {@inheritdoc}
     */
    public function prepare($str)
    {
        if (defined('Symfony\Component\Yaml\Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE')) {
            $flags = Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE;
        } else {
            $flags = true;
        }

        return Yaml::parse($str, $flags);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->getRoot();
    }
}
