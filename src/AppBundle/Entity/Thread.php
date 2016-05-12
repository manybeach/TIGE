<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;
/**
 * Thread
 */
class Thread extends BaseThread
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

}

