<?php


namespace yuchanns\toybox\app\cgroup\subsystems;


use Exception;

class Utils
{
    private static array $subsysCgroupPaths = [];
    /**
     * @param string $subsystem
     * @return string
     * @throws Exception
     */
    public static function findCgroupMountpoint(string $subsystem): string
    {
        if (!$file = fopen("/proc/self/mountinfo", "r")) {
            throw new Exception("open mouninfo failed");
        }

        while (!feof($file)) {
            $txt = fgets($file);
            $fields = explode(" ", $txt);
            foreach (explode(",", $fields[count($fields) - 1]) as $opt) {
                if (trim($opt) === $subsystem) {
                    fclose($file);
                    return $fields[4];
                }
            }
        }

        fclose($file);
        return "";
    }

    /**
     * @param ISubsystem $ins
     * @param string $cgroupPath
     * @param bool $autoCreate
     * @return string
     * @throws Exception
     */
    public static function getCgroupPath(ISubsystem $ins, string $cgroupPath, bool $autoCreate): string
    {
        if (!isset(static::$subsysCgroupPaths[$ins->name()])) {
            $cgroupRoot = static::findCgroupMountpoint($ins->name());
            $absPath = join(DIRECTORY_SEPARATOR, [
                rtrim($cgroupRoot, DIRECTORY_SEPARATOR),
                ltrim($cgroupPath, DIRECTORY_SEPARATOR)
            ]);
            if (!is_dir($absPath) && $autoCreate) {
                if (!mkdir($absPath, 0755)) {
                    throw new Exception("error create cgroup");
                }
            }
            static::$subsysCgroupPaths[$ins->name()] = $absPath;
        }

        return static::$subsysCgroupPaths[$ins->name()];
    }
}