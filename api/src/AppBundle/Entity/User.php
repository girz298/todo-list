<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users",indexes={
 *     @ORM\Index(name="username", columns={"username"}),
 *     @ORM\Index(name="email", columns={"email"}),
 *     @ORM\Index(name="role", columns={"role"}),
 *     @ORM\Index(name="password", columns={"password"}),
 *     @ORM\Index(name="is_active", columns={"is_active"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\Choice(
     *     choices = { "ROLE_USER", "ROLE_MODERATOR", "ROLE_ADMIN" },
     *     message = "Choose a valid role."
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $role;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TaskGroup", mappedBy="user")
     */
    private $taskGroups;

    /**
     * @ORM\Column(name="api_token", type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     * )
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean",  nullable=true)
     */
    private $isActive = null;

    public function __construct()
    {
        $this->setIsActive(true);
        $this->setRole('ROLE_USER');
        $this->taskGroups = new ArrayCollection();
    }

    /**
     * Add task
     *
     * @param TaskGroup $taskGroup
     *
     * @return User
     */
    public function addTaskGroup(TaskGroup $taskGroup)
    {
        $this->taskGroups[] = $taskGroup;

        $taskGroup->setUser($this);

        return $this;
    }

    /**
     * Remove task
     *
     * @param TaskGroup $taskGroup
     */
    public function removeTaskGroup(TaskGroup $taskGroup)
    {
        $taskGroup->setUser(null);

        $this->taskGroups->removeElement($taskGroup);
    }

    /**
     * Get tasks
     *
     * @return ArrayCollection|TaskGroup[]
     */
    public function getTaskGroups()
    {
        return $this->taskGroups;
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

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUserDataToForm()
    {
        return [
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'role' => $this->getRole()
        ];
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     * @return User
     */
    public function setApiToken(string $apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }
}
