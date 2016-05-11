<?php

namespace AppBundle\Entity;

/**
 * Event
 */
class Event
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $eventDescription;

    /**
     * @var \DateTime
     */
    private $eventDate;

    /**
     * @var string
     */
    private $eventPlace;

    /**
     * @var int
     */
    private $eventOwner;

    /**
     * @var int
     */
    private $eventNbParticipants;

    /**
     * @var int
     */
    private $eventMaxParticipant;

    /**
     * @var string
     */
    private $eventMembers;


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
     * Set eventName
     *
     * @param string $eventName
     *
     * @return Event
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * Get eventName
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Set eventDescription
     *
     * @param string $eventDescription
     *
     * @return Event
     */
    public function setEventDescription($eventDescription)
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    /**
     * Get eventDescription
     *
     * @return string
     */
    public function getEventDescription()
    {
        return $this->eventDescription;
    }

    /**
     * Set eventDate
     *
     * @param \DateTime $eventDate
     *
     * @return Event
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * Get eventDate
     *
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * Set eventPlace
     *
     * @param string $eventPlace
     *
     * @return Event
     */
    public function setEventPlace($eventPlace)
    {
        $this->eventPlace = $eventPlace;

        return $this;
    }

    /**
     * Get eventPlace
     *
     * @return string
     */
    public function getEventPlace()
    {
        return $this->eventPlace;
    }

    /**
     * Set eventOwner
     *
     * @param int $eventOwner
     *
     * @return Event
     */
    public function setEventOwner($eventOwner)
    {
        $this->eventOwner = $eventOwner;

        return $this;
    }

    /**
     * Get eventOwner
     *
     * @return int
     */
    public function getEventOwner()
    {
        return $this->eventOwner;
    }

    /**
     * Set eventNbParticipants
     *
     * @param int $eventNbParticipants
     *
     * @return Event
     */
    public function setEventNbParticipants($eventNbParticipants)
    {
        $this->eventNbParticipants = $eventNbParticipants;

        return $this;
    }

    /**
     * Get eventNbParticipants
     *
     * @return int
     */
    public function getEventNbParticipants()
    {
        return $this->eventNbParticipants;
    }

    /**
     * Set eventMaxParticipant
     *
     * @param int $eventMaxParticipant
     *
     * @return Event
     */
    public function setEventMaxParticipant($eventMaxParticipant)
    {
        $this->eventMaxParticipant = $eventMaxParticipant;

        return $this;
    }

    /**
     * Get eventMaxParticipant
     *
     * @return int
     */
    public function getEventMaxParticipant()
    {
        return $this->eventMaxParticipant;
    }

    /**
     * Set eventMembers
     *
     * @param string $eventMembers
     *
     * @return Event
     */
    public function setEventMembers($eventMembers)
    {
        $this->eventMembers = $eventMembers;

        return $this;
    }

    /**
     * Get eventMembers
     *
     * @return string
     */
    public function getEventMembers()
    {
        return $this->eventMembers;
    }

    public function addMember($UserId){
        if (!empty($this->getEventMembers())){
            $this->setEventMembers($this->getEventMembers().';'.$UserId);
        }
        else {
            $this->setEventMembers($UserId);
        }
        $this->setEventNbParticipants($this->getEventNbParticipants() + 1);
    }

    public function supprMember($UserId){
        $arrayMembers = explode(";", $this->getEventMembers());
        $newStringMembers = null;
        foreach ($arrayMembers as $member){
            if ($member != $UserId){
                if (empty($newArrayMembers)){
                    $newStringMembers[] = $member;
                } else {
                    $newStringMembers .= ";".$member;
                }
            }
        }
        $this->setEventMembers($newStringMembers);

        $this->setEventNbParticipants($this->getEventNbParticipants() - 1);
    }
}

