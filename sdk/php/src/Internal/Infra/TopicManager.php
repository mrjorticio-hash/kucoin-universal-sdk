<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

class TopicManager
{
    /** @var array<string, CallbackManager> $topicPrefix */
    private $topicPrefix = [];

    public function getCallbackManager($topic): CallbackManager
    {
        $parts = explode(':', $topic);
        $prefix = (count($parts) === 2 && $parts[1] !== 'all') ? $parts[0] : $topic;

        if (!isset($this->topicPrefix[$prefix])) {
            $this->topicPrefix[$prefix] = new CallbackManager($topic);
        }

        return $this->topicPrefix[$prefix];
    }

    /**
     * @param callable $func function(string $key, CallbackManager $value): bool
     */
    public function range(callable $func)
    {
        foreach ($this->topicPrefix as $key => $value) {
            if (!$func($key, $value)) {
                break;
            }
        }
    }
}