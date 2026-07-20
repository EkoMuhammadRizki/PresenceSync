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

        // Only filter lists of menu items (arrays with numeric keys)
        $hasNumericKeys = false;
        foreach (array_keys($array) as $k) {
            if (is_int($k)) {
                $hasNumericKeys = true;
                break;
            }
        }

        if (!$hasNumericKeys) {
            if (isset($array['sub']['items'])) {
                self::filterMenuPermissions($array['sub']['items']);
            }
            return;
        }

        $user = auth()->user();

        $checkPermission = $checkRole = false;
        $isSiswa = false;
        $isKesiswaan = false;
        $isOrangTua = false;
        $isGuru = false;
        $isAdmin = false;

        if (auth()->check()) {
            // check if the spatie plugin functions exist
            $checkPermission = method_exists($user, 'hasAnyPermission');
            $checkRole       = method_exists($user, 'hasAnyRole');
            
            $isSiswa = \App\Models\Siswa::where('user_id', $user->id)->exists();
            if (!$isSiswa) {
                $isKesiswaan = $user->hasRole('kesiswaan');
                $isOrangTua  = $user->hasRole('orang_tua');
                $isGuru      = \App\Models\Guru::where('user_id', $user->id)->exists();
                $isAdmin     = $user->hasRole('admin');
            }
        }

        foreach ($array as $key => &$value) {
            if (is_callable($value)) {
                continue;
            }

            // Allowed paths definition per role
            if ($isSiswa) {
                $allowedSiswaPaths = ['absensi/siswa/dashboard', 'logout'];
                $siswaModel = \App\Models\Siswa::where('user_id', $user->id)->first();
                if ($siswaModel && $siswaModel->is_sekretaris) {
                    $allowedSiswaPaths[] = 'absensi/siswa/kehadiran-mp';
                    $allowedSiswaPaths[] = 'absensi/siswa/pengaduan';
                }
                if (isset($value['path'])) {
                    if (!in_array($value['path'], $allowedSiswaPaths)) {
                        unset($array[$key]);
                        continue;
                    }
                } elseif (isset($value['sub']['items'])) {
                    self::filterMenuPermissions($value['sub']['items']);
                    if (empty($value['sub']['items'])) {
                        unset($array[$key]);
                        continue;
                    }
                } else {
                    unset($array[$key]);
                    continue;
                }
            } elseif ($isOrangTua) {
                $allowedPaths = [
                    'absensi/orangtua/dashboard',
                    'absensi/orangtua/pengaduan',
                    'absensi/orangtua/profil',
                    'logout'
                ];
                if (isset($value['path'])) {
                    if (!in_array($value['path'], $allowedPaths)) {
                        unset($array[$key]);
                        continue;
                    }
                } elseif (isset($value['sub']['items'])) {
                    self::filterMenuPermissions($value['sub']['items']);
                    if (empty($value['sub']['items'])) {
                        unset($array[$key]);
                        continue;
                    }
                } else {
                    unset($array[$key]);
                    continue;
                }
            } elseif ($isKesiswaan) {
                if (isset($value['title']) && $value['title'] === 'Master Data') {
                    unset($array[$key]);
                    continue;
                }
 
                $allowedPaths = [
                    'absensi/dashboard',
                    'absensi/kesiswaan/dashboard',
                    'absensi/master/siswa',
                    'absensi/master/kelas/data',
                    'absensi/master/kelas/pembagian',
                    'logout'
                ];
                if (isset($value['path'])) {
                    if (!in_array($value['path'], $allowedPaths)) {
                        unset($array[$key]);
                        continue;
                    }
                } elseif (isset($value['sub']['items'])) {
                    self::filterMenuPermissions($value['sub']['items']);
                    if (empty($value['sub']['items'])) {
                        unset($array[$key]);
                        continue;
                    }
                } else {
                    unset($array[$key]);
                    continue;
                }
            } elseif ($isGuru) {
                $allowedPaths = ['absensi/dashboard', 'absensi/kehadiran', 'absensi/guru/kelas-wali', 'absensi/guru/pengaduan', 'logout'];
                if (isset($value['path'])) {
                    if (!in_array($value['path'], $allowedPaths)) {
                        unset($array[$key]);
                        continue;
                    }
                } elseif (isset($value['sub']['items'])) {
                    self::filterMenuPermissions($value['sub']['items']);
                    if (empty($value['sub']['items'])) {
                        unset($array[$key]);
                        continue;
                    }
                } else {
                    unset($array[$key]);
                    continue;
                }
            } else {
                // Admin or fallback: hide student-only dashboard items
                if (isset($value['path']) && in_array($value['path'], ['absensi/siswa/dashboard', 'absensi/siswa/kehadiran-mp', 'absensi/siswa/pengaduan'])) {
                    unset($array[$key]);
                    continue;
                }
                
                // If it is a structural element, clean up recursively
                if (isset($value['sub']['items'])) {
                    self::filterMenuPermissions($value['sub']['items']);
                    if (empty($value['sub']['items'])) {
                        unset($array[$key]);
                        continue;
                    }
                }
            }

            if ($checkPermission && isset($value['permission']) && !$user->hasAnyPermission((array) $value['permission'])) {
                unset($array[$key]);
                continue;
            }

            if ($checkRole && isset($value['role']) && !$user->hasAnyRole((array) $value['role'])) {
                unset($array[$key]);
                continue;
            }

            if (is_array($value)) {
                self::filterMenuPermissions($value);
            }
        }
    }
}
