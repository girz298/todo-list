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
    /*
     * Statuses of daily Task be Eisenhower
     */
    const STATUS_IMPORTANT_URGENT = 1;         // DO NOW
    const STATUS_IMPORTANT_NOT_URGENT = 2;     // DECIDE WHEN TO DO IT
    const STATUS_NOT_IMPORTANT_URGENT = 3;     // DELEGATE IT AWAY
    const STATUS_NOT_IMPORTANT_NOT_URGENT = 4; // DELETE IT

    private $statuses = [
        self::STATUS_IMPORTANT_URGENT,
        self::STATUS_IMPORTANT_NOT_URGENT,
        self::STATUS_NOT_IMPORTANT_URGENT,     // DEFAULT
        self::STATUS_NOT_IMPORTANT_NOT_URGENT
    ];

    /*
     * Types of Task by Franklin
     */
    const TYPE_DAILY_GOAL = 1;          // On THAT TYPE start using Eisenhower method
    const TYPE_WEEKLY_GOAL = 2;
    const TYPE_INTERMEDIATE_GOAL = 3;
    const TYPE_LONG_RANGE_GOAL = 4;
    const TYPE_GOVERNING_VALUE = 5;

    private $types = [
        self::TYPE_DAILY_GOAL,
        self::TYPE_WEEKLY_GOAL,
        self::TYPE_INTERMEDIATE_GOAL,
        self::TYPE_LONG_RANGE_GOAL,
        self::TYPE_GOVERNING_VALUE
    ];

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
    private $group;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * If type  is TYPE_DAILY_GOAL we are using STATUSES by Eisenhower
     * @return Task
     */
    public function setType($type)
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException(
                "Invalid type. Type should be in " . join(', ', $this->types)
            );
        }

        $this->type = $type;

        if (!$this->getStatus() && $type === self::TYPE_DAILY_GOAL) {
            $this->setStatus(self::STATUS_NOT_IMPORTANT_URGENT);
        }

        return $this;
    }

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
        if ($this->getType() != self::TYPE_DAILY_GOAL) {
            throw new \InvalidArgumentException('Statuses active only for DAILY_GOALS');
        }
        if (!in_array($status, $this->statuses)) {
            throw new \InvalidArgumentException(
                "Invalid status. Status should be in " . join(', ', $this->statuses)
            );
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
        $this->setType(self::TYPE_DAILY_GOAL);
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
     * Set group
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
     * Get group
     *
     * @return TaskGroup
     */
    public function getGroup()
    {
        return $this->group;
    }
}
