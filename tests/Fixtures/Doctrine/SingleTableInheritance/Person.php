<?php declare(strict_types=1);

namespace Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "student" = "Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Student",
 *     "teacher" = "Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Teacher",
 * })
 */
abstract class Person
{
    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    public function getId()
    {
        return $this->id;
    }
}
