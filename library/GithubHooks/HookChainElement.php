<?php

namespace GithubHooks;

/**
 * Class HookChainElement
 * @package GithubHooks
 */
class HookChainElement
{


    /**
     * @var HookInterface
     */
    protected $hook;

    /**
     * @var array
     */
    protected $branches = array();

    /**
     * @var array
     */
    protected $events = array();

    /**
     * @param array $branches
     * @return $this
     */
    public function setBranches(array $branches)
    {
        $this->branches = $branches;

        return $this;
    }

    /**
     * @return array
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param array $events
     * @return $this
     */
    public function setEvents(array $events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param HookInterface $hook
     * @return $this
     */
    public function setHook($hook)
    {
        $this->hook = $hook;

        return $this;
    }

    /**
     * @return HookInterface
     */
    public function getHook()
    {
        return $this->hook;
    }
}