<?php

namespace App\Model;

use App\Entity\Task;
use App\Entity\User;

class SessionModel
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var Task|null
     */
    private $task;

    /**
     * @var int|null
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
