<?php

namespace Modules\AddonModule\Traits;

trait AddonHelper
{
    public function get_addons(): array
    {
<<<<<<< HEAD
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if (in_array('Addon', $subDirectories)) {
                $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
=======
        $dir = 'Modules';
        $directories = self::getDirectories($dir);
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories('Modules/' . $directory);
            if (in_array('Addon', $subDirectories)) {
                $addons[] = 'Modules/' . $directory;
>>>>>>> newversion/main
            }
        }

        $array = [];
        foreach ($addons as $item) {
<<<<<<< HEAD
            $fullData = include($item . '/Addon/info.php');
=======
            $fullData = include(base_path($item . '/Addon/info.php'));
>>>>>>> newversion/main
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
<<<<<<< HEAD
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if (in_array('Addon', $subDirectories)) {
                $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
=======
        $dir = 'Modules';
        $directories = self::getDirectories($dir);
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories('Modules/' . $directory);
            if (in_array('Addon', $subDirectories)) {
                $addons[] = 'Modules/' . $directory;
>>>>>>> newversion/main
            }
        }

        $fullData = [];
        foreach ($addons as $item) {
<<<<<<< HEAD
            $info = include($item . '/Addon/info.php');
=======
            $info = include(base_path($item . '/Addon/info.php'));
>>>>>>> newversion/main
            if ($info['is_published']) {
                $fullData[] = include($item . '/Addon/admin_routes.php');
            }
        }

        return $fullData;
    }

    public function get_payment_publish_status(): array
    {
<<<<<<< HEAD
        $dir = base_path('Modules');  // Base directory path
        $directories = self::getDirectories($dir);  // Get the directories inside "Modules"
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . DIRECTORY_SEPARATOR . $directory);  // No need for base_path here
            if ($directory == 'Gateways') {
                if (in_array('Addon', $subDirectories)) {
                    $addons[] = $dir . DIRECTORY_SEPARATOR . $directory;  // Correct path construction
=======
        $dir = 'Modules';
        $directories = self::getDirectories($dir);
        $addons = [];
        foreach ($directories as $directory) {
            $subDirectories = self::getDirectories($dir . '/' . $directory);
            if ($directory == 'Gateways') {
                if (in_array('Addon', $subDirectories)) {
                    $addons[] = $dir . '/' . $directory;
>>>>>>> newversion/main
                }
            }
        }

        $array = [];
        foreach ($addons as $item) {
<<<<<<< HEAD
            $fullData = include($item . '/Addon/info.php');
=======
            $fullData = include(base_path($item . '/Addon/info.php'));
>>>>>>> newversion/main
            $array[] = [
                'is_published' => $fullData['is_published'],
            ];
        }

        return $array;
    }

    function getDirectories(string $path): array
    {
        $directories = [];
<<<<<<< HEAD
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                $directories[] = $item;
            }
=======
        $items = scandir(base_path($path));
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir(base_path($path . '/' . $item)))
                $directories[] = $item;
>>>>>>> newversion/main
        }
        return $directories;
    }
}
<<<<<<< HEAD


=======
>>>>>>> newversion/main
