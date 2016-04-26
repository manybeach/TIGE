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


    
    public function Favourites($bool){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $this->setIdAccount($user);
    }
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

    public function delFavourite($id_user){
        $favourites = explode(';',$this->getIdFavourites());
        $result = false;
        $i=0;
        #On recherche l'élément à supprimer
        foreach ($favourites as $favourite){
            if($id_user== $favourite){
                unset($favourites[$i]);
            }
            $i++;
        }
        #On réassemble le nouveau tableau pour le maj en base
        foreach ($favourites as $favourite){
            $this->idFavourites = (string)$favourite.';';
        }
        
        
        return $result;
    }
}

