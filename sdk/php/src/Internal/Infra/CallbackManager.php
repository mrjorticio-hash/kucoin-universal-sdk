<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketMessageCallback;
use KuCoin\UniversalSDK\Internal\Utils\SubInfo;

class CallbackManager
{
    /** @var array<string, array<string, bool>> */
    private $idTopicMapping = [];

    /** @var array<string, Callback> */
    private $topicCallbackMapping = [];

    /** @var string */
    private $topicPrefix;

    public function __construct($topicPrefix)
    {
        $this->topicPrefix = $topicPrefix;
    }

    public function isEmpty()
    {
        return empty($this->idTopicMapping) && empty($this->topicCallbackMapping);
    }

    /**
     * @return SubInfo[]
     */
    public function getSubInfo()
    {
        $subInfoList = [];

        foreach ($this->idTopicMapping as $topics) {
            $info = new SubInfo($this->topicPrefix, [], null);

            foreach (array_keys($topics) as $topic) {
                $parts = explode(':', $topic);
                if (count($parts) === 2 && $parts[1] !== 'all') {
                    $info->args[] = $parts[1];
                }

                if (isset($this->topicCallbackMapping[$topic])) {
                    $info->callback = $this->topicCallbackMapping[$topic]->callback;
                }
            }

            $subInfoList[] = $info;
        }

        return $subInfoList;
    }

    public function add(SubInfo $subInfo): bool
    {
        $id = $subInfo->toId();
        if (isset($this->idTopicMapping[$id])) {
            return false;
        }

        $topicMap = [];

        foreach ($subInfo->topics() as $topic) {
            if (isset($this->topicCallbackMapping[$topic])) {
                continue;
            }

            $topicMap[$topic] = true;
            $this->topicCallbackMapping[$topic] = new Callback($subInfo->callback, $id, $topic);
        }

        $this->idTopicMapping[$id] = $topicMap;
        return true;
    }

    public function remove($id)
    {
        if (!isset($this->idTopicMapping[$id])) {
            return;
        }

        foreach ($this->idTopicMapping[$id] as $topic => $_) {
            unset($this->topicCallbackMapping[$topic]);
        }

        unset($this->idTopicMapping[$id]);
    }

    /**
     * @param string $topic
     * @return WebSocketMessageCallback|null
     */
    public function get($topic)
    {
        return isset($this->topicCallbackMapping[$topic])
            ? $this->topicCallbackMapping[$topic]->callback
            : null;
    }
}

class Callback
{
    /**
     * @var WebSocketMessageCallback $callback
     */
    public $callback;
    /**
     * @var string $id
     */
    public $id;
    /**
     * var string $topic
     */
    public $topic;

    /**
     * @param WebSocketMessageCallback $callback
     * @param string $id
     * @param $topic
     */
    public function __construct(WebSocketMessageCallback $callback, string $id, $topic)
    {
        $this->callback = $callback;
        $this->id = $id;
        $this->topic = $topic;
    }

}