<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuizRepository")
 * @ORM\Table(name="quizes")
 */
class Quiz
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="teacher", referencedColumnName="id")
     */
    protected $teacher;

    protected $maxpoints;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Exam
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxPoints(){
        return $this->maxpoints;
    }

    public function setMaxPoints($maxpoints){
        $this->maxpoints = $maxpoints;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set teacher
     *
     * @param \AppBundle\Entity\User $teacher
     * @return Quiz
     */
    public function setTeacher(\AppBundle\Entity\User $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return \AppBundle\Entity\User 
     */
    public function getTeacher()
    {
        return $this->teacher;
    }
}
