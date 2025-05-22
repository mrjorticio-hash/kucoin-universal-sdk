<?php


use PHPUnit\Framework\TestCase;

function getProcessTCPConnectionsWithLsof(): int
{
    $pid = getmypid();
    try {
        $cmd = "lsof -iTCP -n -P | grep $pid | wc -l";
        $output = shell_exec($cmd);
        return $output !== null ? (int)trim($output) : 0;
    } catch (Throwable $e) {
        error_log("Error executing lsof: " . $e->getMessage());
        return 0;
    }
}

class ResourceTest extends TestCase
{

}
