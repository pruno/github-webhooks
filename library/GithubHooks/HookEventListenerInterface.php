<?php

namespace GithubHooks;

/**
 * Interface HookEventListenerInterface
 * @package GithubHooks
 */
interface HookEventListenerInterface
{
    /**
     * @param Payload $payload
     * @return void
     */
    public function __invoke(Payload $payload);
}
