<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="attempts")
 */
class Attempt
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * @ORM\ManyToOne(targetEntity="Quiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    protected $quiz;

    /**
     * @ORM\Column(type="integer")
     */
    protected $result;

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
     * Set result
     *
     * @param integer $result
     * @return Attempt
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set quiz
     *
     * @param \AppBundle\Entity\Quiz $quiz
     * @return Attempt
     */
    public function setQuiz(\AppBundle\Entity\Quiz $quiz = null)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz
     *
     * @return \AppBundle\Entity\Quiz 
     */
    public function getQuiz()
    {
        return $this->quiz;
    }
}
