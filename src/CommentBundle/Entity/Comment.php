<?php

namespace CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;

/**
 * Comment
 */
class Comment extends BaseComment
{
    /**
     * @var int
     */
    private $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="\CommentBundle\Entity\Thread")
     */
    protected $thread;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}

