<?php


namespace yuchanns\toybox\app\cgroup\subsystems;


use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Memory implements ISubsystem
{

    function name(): string
    {
        return "memory";
    }

    /**
     * @param string $path
     * @param ResourceCfg $res
     * @return bool
     * @throws Exception
     */
    function set(string $path, ResourceCfg $res): bool
    {
        $subsysCgroupPath = Utils::getCgroupPath($this, $path, true);
        $jpath = join(DIRECTORY_SEPARATOR, [
            rtrim($subsysCgroupPath),
            "memory.limit_in_bytes",
        ]);
        if (!file_put_contents($jpath, $res->getMemoryLimit())) {
            return false;
        }

        fputs(STDOUT, "{$jpath} set success" . PHP_EOL);

        return true;
    }

    /**
     * @param string $path
     * @param int $pid
     * @return bool
     * @throws Exception
     */
    function apply(string $path, int $pid): bool
    {
        $subsysCgroupPath = Utils::getCgroupPath($this, $path, false);
        $jpath = join(DIRECTORY_SEPARATOR, [
            rtrim($subsysCgroupPath),
            "tasks",
        ]);
        if (!file_put_contents($jpath, $pid)) {
            return false;
        }

        fputs(STDOUT, "{$jpath} apply success" . PHP_EOL);

        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws Exception
     */
    function remove(string $path): bool
    {
        $subsysCgroupPath = Utils::getCgroupPath($this, $path, false);
        $it = new RecursiveDirectoryIterator($subsysCgroupPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($subsysCgroupPath);
    }
}