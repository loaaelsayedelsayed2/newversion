<?php

namespace Modules\AddonModule\Traits;

trait AddonHelper
{
    public function get_addons(): array
    {
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if (in_array('Addon', $subDirectories)) {
                $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
            }
        }

        $array = [];
        foreach ($addons as $item) {
            $fullData = include($item . '/Addon/info.php');
            $array[] = [
                'addon_name' => $fullData['name'],
                'software_id' => $fullData['software_id'],
                'is_published' => $fullData['is_published'],
            ];
        }

        return $array;
    }

    public function get_addon_admin_routes(): array
    {
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if (in_array('Addon', $subDirectories)) {
                $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
            }
        }

        $fullData = [];
        foreach ($addons as $item) {
            $info = include($item . '/Addon/info.php');
            if ($info['is_published']) {
                $fullData[] = include($item . '/Addon/admin_routes.php');
            }
        }

        return $fullData;
    }

    public function get_payment_publish_status(): array
    {
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if ($directory == 'Gateways') {
                if (in_array('Addon', $subDirectories)) {
                    $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
                }
            }
        }

        $array = [];
        foreach ($addons as $item) {
            $fullData = include($item . '/Addon/info.php');
            $array[] = [
                'is_published' => $fullData['is_published'],
            ];
        }

        return $array;
    }

    function getDirectories(string $path): array
    {
        $directories = [];
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                $directories[] = $item;
            }
        }
        return $directories;
    }
}


