<?php

namespace AppBundle\Entity;

/**
 * AccountName
 */
class AccountName
{
    /**
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="accountname", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\Column(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="Games", inversedBy="games", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * @ORM\Column(name="game_id", nullable=false)
     */
    private $game_id;


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
     * @return AccountName
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

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return AccountName
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set gameId
     *
     * @param \AppBundle\Entity\Games $gameId
     *
     * @return AccountName
     */
    public function setGameId(\AppBundle\Entity\Games $gameId = null)
    {
        $this->game_id = $gameId;

        return $this;
    }

    /**
     * Get gameId
     *
     * @return \AppBundle\Entity\Games
     */
    public function getGameId()
    {
        return $this->game_id;
    }
}
