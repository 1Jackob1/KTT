<?php

namespace App\Entity;

interface UpdatableInterface
{
    /**
     * @param UpdatableInterface $updator
     *
     * @return $this
     */
    public function update($updator);
}