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
     * @var string
     */
    private $eventPlace;

    /**
     * @var \DateTime
     */
    private $eventDate;

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
}

