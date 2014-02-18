<?php

namespace GithubWebhooks;

/**
 * Interface HookEventListenerInterface
 * @package GithubWebhooks
 */
interface HookEventListenerInterface
{
    /**
     * @param Payload $payload
     * @return void
     */
    public function __invoke(Payload $payload);
}
