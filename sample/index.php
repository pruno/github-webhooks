<?php

use \GithubHooks\Repository;
use \GithubHooks\Server;
use \GithubHooks\Hooks\PullOriginHooks;

include '../vendor/autoload.php';

$repository = new Repository('OwnerName', 'ProjectName');
$repository->addHook(array('master'), new PullOriginHooks('/path/to/working/copy'));

$server = new Server();
$server->addRespitory('ProjectName', $repository);

$server->resolve();