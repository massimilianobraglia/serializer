<?php declare(strict_types=1);

namespace Kcs\Serializer\Tests\Fixture\Doctrine\SingleTableInheritance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Teacher;

/**
 * @ORM\Entity
 */
class Clazz
{
    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Teacher") */
    private $teacher;

    /** @ORM\ManyToMany(targetEntity = "Kcs\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Student") */
    private $students;

    public function __construct(Teacher $teacher, array $students)
    {
        $this->teacher = $teacher;
        $this->students = new ArrayCollection($students);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTeacher()
    {
        return $this->teacher;
    }

    public function getStudents()
    {
        return $this->students;
    }
}
