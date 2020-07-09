<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Session implements TimestampableInterface
{
    use TimestampableTrait;

    public const FULL_CARD = 'full_card';
    public const ELASTICSEARCH_CARD = 'elastica';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sessions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $user;

    /**
     * @var Task
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="sessions")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $task;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $startDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $endDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="valid", type="boolean", nullable=false, options={"default" = true})
     *
     * @JMS\Expose()
     * @JMS\Groups(groups={Session::FULL_CARD, Session::ELASTICSEARCH_CARD})
     */
    private $valid = true;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param User $user
     *
     * @return Session
     */
    public function setUser(User $user): Session
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param Task $task
     *
     * @return Session
     */
    public function setTask(Task $task): Session
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param DateTime $startDate
     *
     * @return Session
     */
    public function setStartDate(DateTime $startDate): Session
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $endDate
     *
     * @return Session
     */
    public function setEndDate(?DateTime $endDate): Session
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     *
     * @return Session
     */
    public function setValid(bool $valid): Session
    {
        $this->valid = $valid;

        return $this;
    }
}
