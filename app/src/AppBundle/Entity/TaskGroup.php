<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Task;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskGroupRepository")
 */
class TaskGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="group")
     */
    private $tasks;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=30, nullable=false)
     */
    private $description;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="taskGroups")
     */
    private $user;

    // TODO: connect TaskGroup to User

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
     * Add task
     *
     * @param Task $task
     *
     * @return TaskGroup
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        $task->setGroup($this);

        return $this;
    }

    /**
     * Remove task
     *
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $task->setGroup(null);

        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return ArrayCollection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TaskGroup
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
     * @param User $user
     * @return TaskGroup
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
         return $this->user;
    }
}
