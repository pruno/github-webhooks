<?php

namespace GithubWebhooks;

use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class HookManager
 * @package GithubWebhooks
 */
class HookManager
{
    /**
     * Any git push to a Repository. This is the default event.
     * @var string
     */
    const EVENT_PUSH = 'push';

    /**
     * Any time an Issue is commented on.
     * @var string
     */
    const EVENT_ISSUE_COMMENT = 'issue_comment';

    /**
     * Any time a Commit is commented on.
     * @var string
     */
    const EVENT_COMMIT_COMMENT = 'commit_comment';

    /**
     * Any time a Repository, Branch, or Tag is created.
     * @var string
     */
    const EVENT_CREATE = 'create';

    /**
     * Any time a Branch or Tag is deleted.
     * @var string
     */
    const EVENT_DELETE = 'delete';

    /**
     * Any time a Pull Request is opened, closed, or synchronized (updated due to a new push in the branch that the pull request is tracking).
     * @var string
     */
    const EVENT_PULL_REQUEST = 'pull_request';

    /**
     * Any time a Commit is commented on while inside a Pull Request review (the Files Changed tab).
     * @var string
     */
    const EVENT_PULL_REQUEST_REVIEW_COMMENT = 'pull_request_review_comment';

    /**
     * Any time a Wiki page is updated.
     * @var string
     */
    const EVENT_GOLLUM = 'gollum';

    /**
     * Any time a User watches the Repository.
     * @var string
     */
    const EVENT_WATCH = 'watch';

    /**
     * Any time a Release is published in the Repository.
     * @var string
     */
    const EVENT_RELEASE = 'release';

    /**
     * Any time a Repository is forked.
     * @var string
     */
    const EVENT_FORK = 'fork';

    /**
     * Any time a User is added as a collaborator to a non-Organization Repository.
     * @var string
     */
    const EVENT_MEMBER = 'member';

    /**
     * Any time a Repository changes from private to public.
     * @var string
     */
    const EVENT_PUBLIC = 'public';

    /**
     * Any time a team is added or modified on a Repository.
     * @var string
     */
    const EVENT_TEAM_ADD = 'team_add';

    /**
     * Any time a Repository has a status update from the API.
     * @var string
     */
    const EVENT_STATUS = 'status';

    /**
     * Any time a Repository has a new deployment created from the API.
     * @var string
     */
    const EVENT_DEPLOYMENT = 'deployment';

    /**
     * Any time a deployment for the Repository has a status update from the API.
     * @var string
     */
    const EVENT_DEPLOYMENT_STATUS = 'deployment_status';

    /**
     * Web Hook debug event
     * @var string
     */
    const EVENT_PING = 'ping';

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var bool
     */
    protected $suppressListenersExceptions = true;

    /**
     * @var array
     */
    protected $hooks = array();

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        if ($this->eventManager === null) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
    }

    /**
     * @param bool $bool
     */
    public function setSuppressListenersExceptions($bool)
    {
        $this->suppressListenersExceptions = (bool) $bool;
    }

    /**
     * @return bool
     */
    public function getSuppressListenersExceptions()
    {
        return $this->suppressListenersExceptions;
    }

    /**
     * @param Hook $hook
     * @param string $event
     * @param HookEventListenerInterface $listener
     * @param int $priority
     */
    public function attach(Hook $hook, $event, HookEventListenerInterface $listener, $priority = 1)
    {
        $hookManager = $this;

        $closure = function(Event $event) use($hook, $listener, $hookManager) {

            if ($event->getTarget() !== $hook) {
                return;
            }

            try {
                $listener($event->getParam('payload'));
            } catch (\Exception $e) {
                if (!$hookManager->getSuppressListenersExceptions()) {
                    throw new \RuntimeException("HookEventListener failure", null, $e);
                }
            }
        };

        if (!$this->hasHook($hook->getId())) {
            $this->addHook($hook);
        }

        $this->getEventManager()->attach($event, $closure, $priority);
    }

    /**
     * @param Hook $hook
     * @return $this
     * @throws \Exception
     */
    public function addHook(Hook $hook)
    {
        if ($this->hasHook($hook->getId())) {
            throw new \Exception("An hook with id '".$hook->getId()."' already exists.");
        }

        $this->hooks[$hook->getId()] = $hook;

        return $this;
    }

    /**
     * @param $hookId
     * @return bool
     */
    public function hasHook($hookId)
    {
        return array_key_exists($hookId, $this->hooks);
    }

    /**
     * @param $hookId
     * @return Hook|null
     */
    public function getHook($hookId)
    {
        return $this->hasHook($hookId) ? $this->hooks[$hookId] : null;
    }

    /**
     * @param Payload $payload
     */
    public function processPayload(Payload $payload)
    {
        if (!$hook = $this->getHook($payload->getHookId())) {
            return;
        }

        $this->getEventManager()->trigger(new Event($payload->getEvent(), $hook, array('payload' => $payload)));
    }
}