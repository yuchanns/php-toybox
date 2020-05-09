<?php


namespace yuchanns\toybox\app\cgroup\subsystems;


interface ISubsystem
{
    function name(): string;

    function set(string $path, ResourceCfg $res): bool;

    function apply(string $path, int $pid): bool;

    function remove(string $path): bool;
}