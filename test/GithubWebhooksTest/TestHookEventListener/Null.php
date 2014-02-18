<?php

namespace GithubWebhooksTest\TestHookEventListener;

use GithubWebhooks\HookEventListenerInterface;
use GithubWebhooks\Payload;

/**
 * Class Null
 * @package GithubWebhooksTest\TestHookEventListener
 */
class Null implements HookEventListenerInterface
{
    /**
     * @var \Closure
     */
    protected $listenerClosure;

    public function __construct(\Closure $listenerClosure)
    {
        $this->listenerClosure = $listenerClosure;
    }

    /**
     * @param Payload $payload
     */
    public function __invoke(Payload $payload)
    {
        call_user_func($this->listenerClosure, $payload);
    }
}