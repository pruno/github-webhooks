<?php

use \GithubHooks\Repository;
use \GithubHooks\Server;
use \GithubHooks\Hooks\PullOriginHooks;

include '../vendor/autoload.php';

$plannify = new Repository('Plannify', 'plannify');
$plannify->addHook(array('develop'), new PullOriginHooks('/var/www/git-auto/plannify'));

$server = new Server();
$server->addRespitory('plannify', $plannify);

$server->resolve();