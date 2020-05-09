<?php


namespace yuchanns\toybox\app\cgroup\subsystems;


class ResourceCfg
{
    private string $memoryLimit;

    public function __construct(string $mem)
    {
        $this->memoryLimit = $mem;
    }

    public function getMemoryLimit(): string
    {
        return $this->memoryLimit;
    }
}