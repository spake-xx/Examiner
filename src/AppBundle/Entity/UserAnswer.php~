<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_answers")
 */
class UserAnswer{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Answer")
     * @ORM\JoinColumn(name="answer", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $answer;

    /**
     * @ORM\ManyToOne(targetEntity="Attempt")
     * @ORM\JoinColumn(name="attempt", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $attempt;

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
     * Set answer
     *
     * @param \AppBundle\Entity\Answer $answer
     * @return UserAnswer
     */
    public function setAnswer(\AppBundle\Entity\Answer $answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return \AppBundle\Entity\Answer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set attempt
     *
     * @param \AppBundle\Entity\Attempt $attempt
     * @return UserAnswer
     */
    public function setAttempt(\AppBundle\Entity\Attempt $attempt = null)
    {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * Get attempt
     *
     * @return \AppBundle\Entity\Attempt 
     */
    public function getAttempt()
    {
        return $this->attempt;
    }
}
