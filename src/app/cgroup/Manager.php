<?php


namespace yuchanns\toybox\app\cgroup;


use yuchanns\toybox\app\cgroup\subsystems\ISubsystem;
use yuchanns\toybox\app\cgroup\subsystems\Memory;
use yuchanns\toybox\app\cgroup\subsystems\ResourceCfg;

class Manager
{
    private string $path;
    private array $ins;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->ins = [
            new Memory(),
        ];
    }

    public function __destruct()
    {
        $this->destory();
    }

    /**
     * @param int $pid
     * @return bool
     */
    public function apply(int $pid): bool
    {
        foreach ($this->ins as $ins) {
            /**
             * @var ISubsystem $ins
             */
            if (!$ins->apply($this->path, $pid)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ResourceCfg $res
     * @return bool
     */
    public function set(ResourceCfg $res): bool
    {
        foreach ($this->ins as $ins) {
            /**
             * @var ISubsystem $ins
             */
            if (!$ins->set($this->path, $res)) {
                return false;
            }
        }

        return true;
    }

    public function destory(): bool
    {
        foreach ($this->ins as $ins) {
            /**
             * @var ISubsystem $ins
             */
            if (!$ins->remove($this->path)) {
                return false;
            }
        }

        return true;
    }
}