<?php

namespace App\Core\Adapters;

/**
 * Adapter class to make the Metronic core lib compatible with the Laravel functions
 *
 * Class Menu
 *
 * @package App\Core\Adapters
 */
class Menu extends \App\Core\Menu
{
    public function build()
    {
        ob_start();

        parent::build();

        return ob_get_clean();
    }

    /**
     * Filter menu item based on the user permission using Spatie plugin
     *
     * @param $array
     */
    public static function filterMenuPermissions(&$array)
    {
        if (!is_array($array)) {
            return;
        }

        $user = auth()->user();

        $checkPermission = $checkRole = false;
        $isSiswa = false;
        if (auth()->check()) {
            // check if the spatie plugin functions exist
            $checkPermission = method_exists($user, 'hasAnyPermission');
            $checkRole       = method_exists($user, 'hasAnyRole');
            $isSiswa         = \App\Models\Siswa::where('user_id', $user->id)->exists();
        }

        foreach ($array as $key => &$value) {
            if (is_callable($value)) {
                continue;
            }

            // Student layout constraints
            if ($isSiswa) {
                // If it's a menu item with a path, filter out non-student paths
                if (isset($value['path'])) {
                    if ($value['path'] !== 'absensi/siswa/dashboard' && $value['path'] !== 'logout') {
                        unset($array[$key]);
                        continue;
                    }
                } else {
                    // It's a header or structural element. We'll filter its children first.
                    if (isset($value['sub']['items'])) {
                        self::filterMenuPermissions($value['sub']['items']);
                        if (empty($value['sub']['items'])) {
                            unset($array[$key]);
                            continue;
                        }
                    } else if (isset($value['title']) && $value['title'] !== 'Kehadiran' && $value['title'] !== 'Logout') {
                        unset($array[$key]);
                        continue;
                    }
                }
            } else {
                // Non-student layout: hide the student dashboard menu
                if (isset($value['path']) && $value['path'] === 'absensi/siswa/dashboard') {
                    unset($array[$key]);
                    continue;
                }
            }

            if ($checkPermission && isset($value['permission']) && !$user->hasAnyPermission((array) $value['permission'])) {
                unset($array[$key]);
            }

            if ($checkRole && isset($value['role']) && !$user->hasAnyRole((array) $value['role'])) {
                unset($array[$key]);
            }

            if (is_array($value)) {
                self::filterMenuPermissions($value);
            }
        }
    }
}
