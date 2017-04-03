<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TaskGroup;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 */
class Task
{

    const IMPORTANT_URGENT = 1;         // DO NOW
    const IMPORTANT_NOT_URGENT = 2;     // DECIDE WHEN TO DO IT
    const NOT_IMPORTANT_URGENT = 3;     // DELEGATE IT AWAY
    const NOT_IMPORTANT_NOT_URGENT = 4; // DELETE IT

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;


    /**
     * @var \DateTime
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $creationDate;


    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $endDate;


    /**
     * @var boolean
     * @ORM\Column(name="state_flag", type="boolean", nullable=true)
     *
     */
    private $stateFlag = true;

    /**
     * @ORM\ManyToOne(targetEntity="TaskGroup", inversedBy="tasks")
     */
    private $group = null;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 4;

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     * @return Task
     */
    public function setStatus($status)
    {
        $statuses = [
            self::IMPORTANT_URGENT,
            self::IMPORTANT_NOT_URGENT,
            self::NOT_IMPORTANT_URGENT,
            self::NOT_IMPORTANT_NOT_URGENT
        ];

        if (!in_array($status, $statuses)){
            throw new \InvalidArgumentException(
                "Invalid status. Status should be in " . join(', ', $statuses
                ));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getStateFlag()
    {
        return $this->stateFlag;
    }

    /**
     * @param boolean $stateFlag
     *
     * @return Task
     */
    public function setStateFlag($stateFlag)
    {
        $this->stateFlag = $stateFlag;

        return $this;
    }


    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set end date of Task
     *
     * @param \DateTime $endDate
     *
     * @return Task
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get end date of Task
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set groupId
     *
     * @param TaskGroup $group
     * @return Task
     */
    public function setGroup(TaskGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return TaskGroup
     */
    public function getGroup()
    {
        return $this->group;
    }
}
