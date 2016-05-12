<?php

namespace AppBundle\Entity;

/**
 * Games
 */
class Games
{
    /**
     * @var int
     */
    private $id;

//    /**
//     * @ORM\Column(type="string", length=100)
//     */


    private $name;


    /**
     * Get id
     *
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Games
     */
    public function setName($name)
    {
        $this->name = $name;

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
}
