<?php

namespace GithubWebhooks;

use GithubWebhooks\Hooks\Pull;

include '../vendor/autoload.php';

$webhook = new Hook('custom_id', 'pruno', 'github-hooks');

$server = new Server();
$server->getHookManager()->setSuppressListenersExceptions(false);
$server->getHookManager()->attach($webhook, HookManager::EVENT_PING, new Pull('/path/to/clone'));

$server->resolve();