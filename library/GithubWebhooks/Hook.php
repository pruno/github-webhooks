<?php

namespace GithubWebhooks;

/**
 * Class Hook
 * @package GithubWebhooks
 */
class Hook
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @param string $id
     * @param string $owner
     * @param string $repository
     */
    public function __construct($id, $owner, $repository)
    {
        $this->id = (string) $id;
        $this->owner = (string) $owner;
        $this->repository = (string) $repository;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }
}