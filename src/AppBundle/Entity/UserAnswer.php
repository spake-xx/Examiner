<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_answers")
 */
class UserAnswer{
    /**
     * @ORM\ManyToOne(targetEntity="Answer")
     * @ORM\JoinColumn(name="answer", referencedColumnName="id")
     */
    protected $answer;

    /**
     * @ORM\ManyToOne(targetEntity="Attempt")
     * @ORM\JoinColumn(name="attempt", referencedColumnName="id")
     */
    protected $attempt;
}