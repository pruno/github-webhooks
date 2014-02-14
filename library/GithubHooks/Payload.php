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
        $this->setContent($data); //FIXME need to be parsed
        $this->setMetadata('event', $event);
        $this->setMetadata('version', 'FIXME');
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

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->getMetadata('version');
    }
}