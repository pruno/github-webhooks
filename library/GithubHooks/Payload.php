<?php

namespace GithubHooks;

use Zend\Stdlib\Message;

/**
 * Class Payload
 * @package GithubHooks
 */
class Payload extends Message
{
    /**
     * @var array
     */
    protected $content;

    /**
     * @param mixed $data
     * @param string $event
     */
    public function __construct($data, $event)
    {
        $this->setContent(json_decode($data, true));
        $this->setMetadata('event', $event);
    }

    /**
     * @return int
     */
    public function getHookId()
    {
        return (int) $this->content['hook_id'];
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->getMetadata('event');
    }
}