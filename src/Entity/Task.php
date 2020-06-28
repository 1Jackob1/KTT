<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task implements TimestampableInterface
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
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default" = 1})
     */
    private $priority = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="estimate", type="integer", nullable=true)
     */
    private $estimate;

    /**
     * @var User[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="tasks")
     */
    private $executors;

    /**
     * @var Session[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="task")
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
     * @return string
     */
    public function getTitle(): string
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
     * @return string
     */
    public function getDescription(): string
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
}
