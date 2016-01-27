<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="answers")
 */
class Answer
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * @ORM\ManyToOne(targetEntity="Attempt")
     * @ORM\JoinColumn(name="attempt_id", referencedColumnName="id")
     */
    protected $attempt_id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $answer;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question_id;
    

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
     * Set attempt_id
     *
     * @param \AppBundle\Entity\Attempt $attemptId
     * @return Answer
     */
    public function setAttemptId(\AppBundle\Entity\Attempt $attemptId = null)
    {
        $this->attempt_id = $attemptId;

        return $this;
    }

    /**
     * Get attempt_id
     *
     * @return \AppBundle\Entity\Attempt 
     */
    public function getAttemptId()
    {
        return $this->attempt_id;
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set question_id
     *
     * @param \AppBundle\Entity\Question $questionId
     * @return Answer
     */
    public function setQuestionId(\AppBundle\Entity\Question $questionId = null)
    {
        $this->question_id = $questionId;

        return $this;
    }

    /**
     * Get question_id
     *
     * @return \AppBundle\Entity\Question 
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }
}
