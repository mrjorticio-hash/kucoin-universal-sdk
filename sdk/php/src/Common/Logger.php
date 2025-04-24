<?php

namespace KuCoin\UniversalSDK\Common;

use Throwable;

class Logger
{
    /** @var callable|null */
    private static $customLogger = null;

    /** @var string $level */
    private static $level = 'info';

    /** @var string $format */
    private static $format = '[{time}] [{level}] {message}{context}';

    private const LEVEL_ORDER = ['debug', 'info', 'warn', 'error'];

    /**
     * Inject a custom logger. Callable: function($level, $message, $context = [])
     */
    public static function setLogger(callable $logger)
    {
        self::$customLogger = $logger;
    }

    /**
     * Set minimum log level. Allowed: debug, info, warn, error
     */
    public static function setLevel(string $level)
    {
        self::$level = strtolower($level);
    }

    /**
     * Set custom format. Placeholders: {time}, {level}, {message}, {context}
     */
    public static function setFormat(string $format)
    {
        self::$format = $format;
    }

    /**
     * Get the current log level.
     *
     * @return string
     */
    public static function getLevel(): string
    {
        return self::$level;
    }

    /**
     * Check if current log level is debug.
     *
     * @return bool
     */
    public static function isDebugEnabled(): bool
    {
        return self::getLevel() === 'debug';
    }

    public static function debug(string $message, array $context = [])
    {
        self::log('debug', $message, $context);
    }

    public static function info(string $message, array $context = [])
    {
        self::log('info', $message, $context);
    }

    public static function warn(string $message, array $context = [])
    {
        self::log('warn', $message, $context);
    }

    public static function error(string $message, array $context = [])
    {
        self::log('error', $message, $context);
    }

    private static function log(string $level, string $message, array $context = [])
    {
        if (!self::shouldLog($level)) {
            return;
        }

        if (self::$customLogger) {
            call_user_func(self::$customLogger, $level, $message, $context);
        } else {
            $output = self::formatLog($level, $message, $context);
            error_log($output);
        }
    }

    private static function shouldLog(string $level): bool
    {
        $curIndex = array_search(self::$level, self::LEVEL_ORDER);
        $logIndex = array_search($level, self::LEVEL_ORDER);
        return $logIndex !== false && $curIndex !== false && $logIndex >= $curIndex;
    }

    private static function formatLog(string $level, string $message, array $context = []): string
    {
        $pairs = [];
        foreach ($context as $k => $v) {
            if ($v instanceof Throwable) {
                $pairs[] = "{$k}=" . get_class($v) . ": " . $v->getMessage();
                $pairs[] = "trace=" . str_replace(PHP_EOL, ' | ', $v->getTraceAsString());
            } else {
                $pairs[] = "$k=$v";
            }
        }

        $contextStr = $pairs ? ' ' . implode(' ', $pairs) : '';

        $replacements = [
            '{time}' => date('Y-m-d H:i:s'),
            '{level}' => strtoupper($level),
            '{message}' => $message,
            '{context}' => $contextStr,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), self::$format);
    }

}
