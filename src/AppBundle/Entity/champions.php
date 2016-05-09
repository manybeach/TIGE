<?php

namespace AppBundle\Entity;

/**
 * champions
 */
class champions
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idChampion;

    /**
     * @var string
     */
    private $championName;


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
     * Set idChampion
     *
     * @param integer $idChampion
     *
     * @return champions
     */
    public function setIdChampion($idChampion)
    {
        $this->idChampion = $idChampion;

        return $this;
    }

    /**
     * Get idChampion
     *
     * @return int
     */
    public function getIdChampion()
    {
        return $this->idChampion;
    }

    /**
     * Set championName
     *
     * @param string $championName
     *
     * @return champions
     */
    public function setChampionName($championName)
    {
        $this->championName = $championName;

        return $this;
    }

    /**
     * Get championName
     *
     * @return string
     */
    public function getChampionName()
    {
        return $this->championName;
    }
}
