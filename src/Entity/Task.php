<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Task implements TimestampableInterface, UpdatableInterface
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
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default" = 1})
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $priority = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="estimate", type="integer", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $estimate = 1;

    /**
     * @var User[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="tasks")
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $executors;

    /**
     * @var Session[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="task")
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={User::FULL_CARD})
     */
    private $sessions;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->executors = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return Task
     */
    public function setTitle(string $title): Task
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @return Task
     */
    public function setDescription(string $description): Task
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param int $priority
     *
     * @return Task
     */
    public function setPriority(int $priority): Task
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $estimate
     *
     * @return Task
     */
    public function setEstimate(int $estimate): Task
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * @return int
     */
    public function getEstimate(): int
    {
        return $this->estimate;
    }

    /**
     * @param User[]|Collection $executors
     *
     * @return Task
     */
    public function setExecutors($executors): Task
    {
        $this->executors = $executors;

        return $this;
    }

    /**
     * @param User $executor
     *
     * @return Task
     */
    public function addExecutor(User $executor): Task
    {
        $this->getExecutors()->set($executor->getId(), $executor);

        return $this;
    }

    /**
     * @param User $executor
     *
     * @return Task
     */
    public function removeExecutor(User $executor): Task
    {
        $this->getExecutors()->remove($executor->getId());

        return $this;
    }

    /**
     * @return User[]|Collection
     */
    public function getExecutors()
    {
        return $this->executors;
    }

    /**
     * @param Session[]|Collection $sessions
     *
     * @return Task
     */
    public function setSessions($sessions): Task
    {
        $this->sessions = $sessions;

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return Task
     */
    public function addSession(Session $session): Task
    {
        $this->getSessions()->set($session->getId(), $session);

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return Task
     */
    public function removeSession(Session $session): Task
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

    /**
     * @inheritDoc
     */
    public function update($task)
    {
        $this
            ->setTitle($task->getTitle())
            ->setDescription($task->getDescription())
            ->setEstimate($task->getEstimate())
            ->setPriority($task->getPriority())
            ->setExecutors($task->getExecutors())
            ->setSessions($task->getSessions())
        ;
    }
}
