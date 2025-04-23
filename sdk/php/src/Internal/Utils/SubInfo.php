<?php

namespace KuCoin\UniversalSDK\Internal\Utils;

use Exception;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketMessageCallback;

class SubInfo
{
    const EMPTY_ARGS_STR = 'EMPTY_ARGS';

    /** @var string */
    public $prefix;

    /** @var string[] */
    public $args;

    /** @var WebSocketMessageCallback|null */
    public $callback;

    /**
     * @param string $prefix
     * @param string[] $args
     * @param WebSocketMessageCallback|null $callback
     */
    public function __construct(string $prefix, array $args = [], WebSocketMessageCallback $callback = null)
    {
        $this->prefix = $prefix;
        $this->args = $args;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function toId(): string
    {
        if (empty($this->args)) {
            $argsStr = self::EMPTY_ARGS_STR;
        } else {
            $sortedArgs = $this->args;
            sort($sortedArgs, SORT_STRING);
            $argsStr = implode(',', $sortedArgs);
        }

        return $this->prefix . '@@' . $argsStr;
    }

    /**
     * @param string $id
     * @param callable|null $callback
     * @return SubInfo
     * @throws Exception
     */
    public static function fromId(string $id, WebSocketMessageCallback $callback = null): SubInfo
    {
        $parts = explode('@@', $id, 2);
        if (count($parts) !== 2) {
            throw new Exception("SubInfo::fromId: invalid id format: $id");
        }

        $prefix = $parts[0];
        $args = ($parts[1] === self::EMPTY_ARGS_STR) ? [] : explode(',', $parts[1]);

        return new self($prefix, $args, $callback);
    }

    /**
     * @return string[]
     */
    public function topics(): array
    {
        if (empty($this->args)) {
            return [$this->prefix];
        }

        $topics = [];
        foreach ($this->args as $arg) {
            $topics[] = $this->prefix . ':' . $arg;
        }

        return $topics;
    }

    /**
     * @return string
     */
    public function subTopic(): string
    {
        if (empty($this->args)) {
            return $this->prefix;
        }

        return $this->prefix . ':' . implode(',', $this->args);
    }
}
