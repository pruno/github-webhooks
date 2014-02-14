<?php

namespace GithubHooks;

/**
 * Class Payload
 * @package GithubHooks
 */
class Payload
{
    /**
     * @var array
     */
    protected $content;

    /**
     * @var string
     */
    protected $event;

    /**
     * @var string
     */
    protected $hookId;

    /**
     * @param mixed $data
     * @param string $event
     * @param string $hookId
     */
    public function __construct($data, $event, $hookId)
    {
        $this->content = json_decode($data, true);
        $this->event = $event;
        $this->hookId = $hookId;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getHookId()
    {
        return $this->hookId;
    }
}