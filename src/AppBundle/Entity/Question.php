<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="questions")
 */
class Question
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
     * @ORM\Column(type="string", length=200)
     */
    protected $question;
    
    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    protected $image;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $a;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $b;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $c;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $d;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $correct;

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
     * Set question
     *
     * @param string $question
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set image
     *
     * @param integer $image
     * @return Question
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return integer 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set a
     *
     * @param string $a
     * @return Question
     */
    public function setA($a)
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get a
     *
     * @return string 
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * Set b
     *
     * @param string $b
     * @return Question
     */
    public function setB($b)
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Get b
     *
     * @return string 
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * Set c
     *
     * @param string $c
     * @return Question
     */
    public function setC($c)
    {
        $this->c = $c;

        return $this;
    }

    /**
     * Get c
     *
     * @return string 
     */
    public function getC()
    {
        return $this->c;
    }

    /**
     * Set d
     *
     * @param string $d
     * @return Question
     */
    public function setD($d)
    {
        $this->d = $d;

        return $this;
    }

    /**
     * Get d
     *
     * @return string 
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * Set exam_id
     *
     * @param \AppBundle\Entity\Exam $examId
     * @return Question
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

    /**
     * Set correct
     *
     * @param string $correct
     * @return Question
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;

        return $this;
    }

    /**
     * Get correct
     *
     * @return string 
     */
    public function getCorrect()
    {
        return $this->correct;
    }
    
    public function getCorrectText(){
    	$correct = $this->correct;
    	switch($correct){
    		case 'a':
    			return $this->a;
    			break;
    		case 'b':
    			return $this->b;
    			break;
    		case 'c':
    			return $this->c;
    			break;
    		case 'd':
    			return $this->c;
    			break;
    		default:
    			return;
    			break;
    	}
    }
}
