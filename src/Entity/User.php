<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="second_name", type="string", nullable=false)
     */
    private $secondName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     */
    private $timeZone;

    /**
     * @var Task[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Task", mappedBy="executors")
     * @ORM\JoinTable(
     *     name="users__tasks",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false)}
     *     )
     */
    private $tasks;

    /**
     * @var Session[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="user")
     */
    private $sessions;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId() . ' ' . $this->getFirstName() . ' ' . $this->getSecondName() . ' ' . $this->getLastName();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $secondName
     *
     * @return User
     */
    public function setSecondName(string $secondName): User
    {
        $this->secondName = $secondName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->secondName;
    }

    /**
     * @param string|null $lastName
     *
     * @return User
     */
    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $timeZone
     *
     * @return User
     */
    public function setTimeZone(string $timeZone): User
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * @param Task[]|Collection $tasks
     *
     * @return User
     */
    public function setTasks($tasks): User
    {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * @param Task $task
     *
     * @return User
     */
    public function addTask(Task $task): User
    {
        $this->getTasks()->set($task->getId(), $task);
        $task->addExecutor($this);

        return $this;
    }

    /**
     * @param Task $task
     *
     * @return User
     */
    public function removeTask(Task $task): User
    {
        $this->getTasks()->remove($task->getId());
        $task->removeExecutor($this);

        return $this;
    }

    /**
     * @return Task[]|Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param Session[]|Collection $sessions
     *
     * @return User
     */
    public function setSessions($sessions): User
    {
        $this->sessions = $sessions;

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return User
     */
    public function addSession(Session $session): User
    {
        $this->getSessions()->set($session->getId(), $session);

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return User
     */
    public function removeSession(Session $session): User
    {
        $this->getSessions()->remove($session->getId());

        return $this;
    }

    /**
     * @return Session[]|Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
