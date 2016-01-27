<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="questions")
 */
class Exam
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $question;
    
    /**
     * @ORM\Column(type="integer", length=5)
     */
    protected $image;
    
    /**
     * @ORM\Column(type="string", length=100)
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
