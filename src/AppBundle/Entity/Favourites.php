<?php

namespace AppBundle\Entity;

/**
 * Favourites
 */
class Favourites
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idAccount;

    /**
     * @var string
     */
    private $idFavourites;


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
     * Set idAccount
     *
     * @param integer $idAccount
     *
     * @return Favourites
     */
    public function setIdAccount($idAccount)
    {
        $this->idAccount = $idAccount;

        return $this;
    }

    /**
     * Get idAccount
     *
     * @return int
     */
    public function getIdAccount()
    {
        return $this->idAccount;
    }

    /**
     * Set idFavourites
     *
     * @param string $idFavourites
     *
     * @return Favourites
     */
    public function setIdFavourites($idFavourites)
    {
        if(empty($this->getIdFavourites()))
            $this->idFavourites = (string)$idFavourites.';';
        else    
            $this->idFavourites = $this->getIdFavourites().(string)$idFavourites.';';

        return $this;
    }

    /**
     * Get idFavourites
     *
     * @return string
     */
    public function getIdFavourites()
    {
        return $this->idFavourites;
    }
}

