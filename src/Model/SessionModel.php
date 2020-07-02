<?php

namespace App\Model;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class SessionModel
{
    /**
     * @var int|null
     *
     * @Assert\NotNull()
     */
    private $id;

    /**
     * @var User|null
     *
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @var Task|null
     *
     * @Assert\NotNull()
     */
    private $task;

    /**
     * @var int|null
     *
     * @Assert\GreaterThan(value="0")
     */
    private $timestamp;

    /**
     * @param int|null $id
     *
     * @return SessionModel
     */
    public function setId(?int $id): SessionModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param User|null $user
     *
     * @return SessionModel
     */
    public function setUser(?User $user): SessionModel
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param Task|null $task
     *
     * @return SessionModel
     */
    public function setTask(?Task $task): SessionModel
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param int|null $timestamp
     *
     * @return SessionModel
     */
    public function setTimestamp(?int $timestamp): SessionModel
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }
}
