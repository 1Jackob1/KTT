<?php

namespace App\Entity;

use App\Form\UserFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class User implements TimestampableInterface, UpdatableInterface
{
    use TimestampableTrait;

    public const FULL_CARD = 'full_card';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\NotNull()
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="second_name", type="string", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\NotBlank()
     */
    private $secondName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="timezone", type="string", nullable=false, options={"default" = "Asia/Vladivostok"})
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\Timezone()
     */
    private $timezone;

    /**
     * @var Task[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Task", inversedBy="executors")
     * @ORM\JoinTable(
     *     name="users__tasks",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false)}
     *     )
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\Collection()
     */
    private $tasks;

    /**
     * @var Session[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="user")
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     *
     * @Assert\Collection()
     */
    private $sessions;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId() . ' ' . $this->getFirstName() . ' ' . $this->getSecondName() . ' ' . $this->getLastName();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return string|null
     */
    public function getFirstName(): ?string
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
     * @return string|null
     */
    public function getSecondName(): ?string
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
     * @param string $timezone
     *
     * @return User
     */
    public function setTimezone(string $timezone): User
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
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
        if (!$this->getTasks()->contains($task)) {
            $this->getTasks()->add($task);
            $task->addExecutor($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     *
     * @return User
     */
    public function removeTask(Task $task): User
    {
        $this->getTasks()->removeElement($task);
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
        if (!$this->getSessions()->contains($session)) {
            $this->getSessions()->add($session);
            $session->setUser($this);
        }

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return User
     */
    public function removeSession(Session $session): User
    {
        $this->getSessions()->removeElement($session);

        return $this;
    }

    /**
     * @return Session[]|Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * @inheritDoc
     */
    public function update($user)
    {
        return $this
            ->setFirstName($user->getFirstName())
            ->setSecondName($user->getSecondName())
            ->setLastName($user->getLastName())
            ->setTimezone($user->getTimezone())
            ->setTasks($user->getTasks())
            ->setSessions($user->getSessions())
        ;
    }
}
