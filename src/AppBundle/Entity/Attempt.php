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
     * @ORM\ManyToOne(targetEntity="Exam")
     * @ORM\JoinColumn(name="exam_id", referencedColumnName="id")
     */
    protected $exam_id;
    
    

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
     * Set exam_id
     *
     * @param \AppBundle\Entity\Exam $examId
     * @return Attempt
     */
    public function setExamId(\AppBundle\Entity\Exam $examId = null)
    {
        $this->exam_id = $examId;

        return $this;
    }

    /**
     * Get exam_id
     *
     * @return \AppBundle\Entity\Exam 
     */
    public function getExamId()
    {
        return $this->exam_id;
    }
}
