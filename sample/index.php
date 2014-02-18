<?php

namespace GithubWebhooks;

use GithubWebhooks\HookEventListener\Pull;

include '../vendor/autoload.php';

$webhook = new Hook('custom_id', 'pruno', 'github-webhooks');

$server = new Server();
$server->getHookManager()->setSuppressListenersExceptions(false);
// Replace the 4th argument with the path to your deploy-key (if it's not your default ssh-key)
$server->getHookManager()->attach($webhook, HookManager::EVENT_PUSH, new Pull('/path/to/working/copy', 'orign', 'master', null));

$server->resolve();