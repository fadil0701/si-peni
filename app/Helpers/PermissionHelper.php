<?php

namespace App\Helpers;

use App\Models\User;

class PermissionHelper
{
    /**
     * Mapping role ke permission/modul yang bisa diakses
     */
    public static function getRolePermissions(): array
    {
        return [
            // 1. ADMIN SISTEM
            'admin' => [
                'master-manajemen.*',
                'master.*',
                'master-data.*',
                'inventory.*',
                'transaction.*',
                'asset.*',
                'planning.*',
                'procurement.*',
                'finance.*',
                'reports.*',
                'admin.*',
            ],
            
            // 2. PEGAWAI (PEMOHON) / ADMIN UNIT
            'pegawai' => [
                'user.dashboard',
                'user.assets.*',
                'user.requests.*',
                'transaction.permintaan-barang.create',
                'transaction.permintaan-barang.store',
                'transaction.permintaan-barang.index',
                'transaction.permintaan-barang.show',
                'transaction.permintaan-barang.edit',
                'transaction.penerimaan-barang.*',
                // Akses inventory untuk gudang unit
                'inventory.data-stock.index', // Hanya untuk gudang unit
                'inventory.data-inventory.index', // Hanya untuk gudang unit
                'inventory.data-inventory.show', // Hanya untuk gudang unit
                'transaction.retur.*', // Return ke gudang pusat
                // Akses Aset & KIR untuk unit kerja mereka sendiri
                'asset.register-aset.index', // View register aset unit mereka
                'asset.register-aset.show', // View detail register aset unit mereka
                'asset.register-aset.edit', // Update register aset unit mereka
                'asset.register-aset.update', // Update register aset unit mereka
            ],
            
            // 3. KEPALA UNIT
            'kepala_unit' => [
                'transaction.permintaan-barang.index',
                'transaction.permintaan-barang.show',
                'transaction.approval.index', // Bisa melihat daftar approval
                'transaction.approval.show', // Bisa melihat detail approval
                'transaction.approval.mengetahui', // Action khusus untuk mengetahui
                // Akses inventory untuk gudang unit
                'inventory.data-stock.index', // Hanya untuk gudang unit
                'inventory.data-inventory.index', // Hanya untuk gudang unit
                'inventory.data-inventory.show', // Hanya untuk gudang unit
                'transaction.penerimaan-barang.*',
                'transaction.retur.*', // Return ke gudang pusat
                // Akses Aset & KIR untuk unit kerja mereka sendiri
                'asset.register-aset.index', // View register aset unit mereka
                'asset.register-aset.show', // View detail register aset unit mereka
                'asset.register-aset.edit', // Update register aset unit mereka
                'asset.register-aset.update', // Update register aset unit mereka
            ],
            
            // 4. KASUBBAG TU (verifikasi)
            'kasubbag_tu' => [
                'transaction.permintaan-barang.index',
                'transaction.permintaan-barang.show',
                'transaction.approval.index', // Bisa melihat daftar approval
                'transaction.approval.show', // Bisa melihat detail approval
                'transaction.approval.verifikasi', // Action khusus untuk verifikasi
                'transaction.approval.kembalikan', // Bisa mengembalikan jika tidak lengkap
                // Akses untuk monitoring dan laporan
                'reports.*', // Bisa melihat semua laporan
                // Akses untuk data inventory dan stock
                'inventory.data-stock.index', // Bisa melihat data stock
                'inventory.data-stock.show', // Bisa melihat detail stock
                'inventory.data-inventory.index', // Bisa melihat data inventory
                'inventory.data-inventory.show', // Bisa melihat detail inventory
            ],
            
            // 5. KEPALA PUSAT (PIMPINAN) - approve/reject
            'kepala_pusat' => [
                'transaction.permintaan-barang.index',
                'transaction.permintaan-barang.show',
                'transaction.approval.*',
                'reports.*',
            ],
            
            // 6. ADMIN GUDANG / PENGURUS BARANG
            'admin_gudang' => [
                'inventory.*',
                'transaction.distribusi.*',
                'transaction.penerimaan-barang.*',
                'transaction.approval.index',
                'transaction.approval.show',
                'transaction.approval.disposisi', // Bisa melihat disposisi
                'asset.register-aset.*',
                'reports.stock-gudang',
                'master.gudang.index',
                'master.gudang.show',
                'master-data.data-barang.*',
            ],
            
            // 7. UNIT TERKAIT
            'perencanaan' => [
                'transaction.approval.index',
                'transaction.approval.show',
                'transaction.approval.disposisi',
            ],
            'pengadaan' => [
                'transaction.approval.index',
                'transaction.approval.show',
                'transaction.approval.disposisi',
            ],
            'keuangan' => [
                'transaction.approval.index',
                'transaction.approval.show',
                'transaction.approval.disposisi',
            ],
        ];
    }

    /**
     * Check if user can access a route
     * Priority: Database permissions > Static permissions
     */
    public static function canAccess(User $user, string $permission): bool
    {
        // Admin selalu bisa akses semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // First, check database permissions (dynamic)
        if ($user->hasPermission($permission)) {
            return true;
        }

        // Fallback to static permissions (for backward compatibility)
        $rolePermissions = self::getRolePermissions();
        $userRoles = $user->roles->pluck('name')->toArray();

        foreach ($userRoles as $role) {
            if (!isset($rolePermissions[$role])) {
                continue;
            }

            $permissions = $rolePermissions[$role];
            
            foreach ($permissions as $allowedPermission) {
                // Exact match
                if ($allowedPermission === $permission) {
                    return true;
                }
                
                // Wildcard match (e.g., 'inventory.*' matches 'inventory.data-stock.index')
                if (str_ends_with($allowedPermission, '.*')) {
                    $prefix = str_replace('.*', '', $allowedPermission);
                    if (str_starts_with($permission, $prefix . '.')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get accessible menu items for user
     */
    public static function getAccessibleMenus(User $user): array
    {
        $menus = [
            'dashboard' => ['route' => 'user.dashboard', 'roles' => ['*']],
            'master-manajemen' => [
                'route' => null,
                'roles' => ['admin'],
                'submenus' => [
                    'master-pegawai' => ['route' => 'master-manajemen.master-pegawai.index', 'roles' => ['admin']],
                    'master-jabatan' => ['route' => 'master-manajemen.master-jabatan.index', 'roles' => ['admin']],
                    'unit-kerja' => ['route' => 'master.unit-kerja.index', 'roles' => ['admin']],
                    'gudang' => ['route' => 'master.gudang.index', 'roles' => ['admin', 'admin_gudang']],
                    'ruangan' => ['route' => 'master.ruangan.index', 'roles' => ['admin']],
                    'program' => ['route' => 'master.program.index', 'roles' => ['admin']],
                    'kegiatan' => ['route' => 'master.kegiatan.index', 'roles' => ['admin']],
                    'sub-kegiatan' => ['route' => 'master.sub-kegiatan.index', 'roles' => ['admin']],
                ],
            ],
            'master-data' => [
                'route' => null,
                'roles' => ['admin', 'admin_gudang'],
                'submenus' => [
                    'aset' => ['route' => 'master-data.aset.index', 'roles' => ['admin']],
                    'kode-barang' => ['route' => 'master-data.kode-barang.index', 'roles' => ['admin']],
                    'kategori-barang' => ['route' => 'master-data.kategori-barang.index', 'roles' => ['admin']],
                    'jenis-barang' => ['route' => 'master-data.jenis-barang.index', 'roles' => ['admin']],
                    'subjenis-barang' => ['route' => 'master-data.subjenis-barang.index', 'roles' => ['admin']],
                    'data-barang' => ['route' => 'master-data.data-barang.index', 'roles' => ['admin', 'admin_gudang']],
                    'satuan' => ['route' => 'master-data.satuan.index', 'roles' => ['admin']],
                    'sumber-anggaran' => ['route' => 'master-data.sumber-anggaran.index', 'roles' => ['admin']],
                ],
            ],
            'inventory' => [
                'route' => null,
                'roles' => ['admin', 'admin_gudang', 'kepala_unit', 'pegawai', 'kasubbag_tu'],
                'submenus' => [
                    'data-stock' => ['route' => 'inventory.data-stock.index', 'roles' => ['admin', 'admin_gudang', 'kepala_unit', 'pegawai', 'kasubbag_tu']],
                    'data-inventory' => ['route' => 'inventory.data-inventory.index', 'roles' => ['admin', 'admin_gudang', 'kepala_unit', 'pegawai', 'kasubbag_tu']],
                ],
            ],
            'transaksi' => [
                'route' => null,
                'roles' => ['admin', 'admin_gudang', 'admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi', 'kepala_pusat', 'kasubbag_tu', 'kepala_unit', 'pegawai', 'perencanaan', 'pengadaan', 'keuangan'],
                'submenus' => [
                    'permintaan-barang' => ['route' => 'transaction.permintaan-barang.index', 'roles' => ['admin', 'pegawai', 'kepala_unit', 'kasubbag_tu', 'kepala_pusat']],
                    'approval' => ['route' => 'transaction.approval.index', 'roles' => ['admin', 'kepala_unit', 'kasubbag_tu', 'kepala_pusat', 'admin_gudang', 'admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi', 'perencanaan', 'pengadaan', 'keuangan']],
                    'distribusi' => ['route' => 'transaction.distribusi.index', 'roles' => ['admin', 'admin_gudang', 'admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi']],
                    'penerimaan-barang' => ['route' => 'transaction.penerimaan-barang.index', 'roles' => ['admin', 'admin_gudang', 'admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi', 'pegawai', 'kepala_unit']],
                    'retur' => ['route' => 'transaction.retur.index', 'roles' => ['admin', 'admin_gudang', 'admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi', 'pegawai', 'kepala_unit']],
                ],
            ],
            'aset-kir' => ['route' => 'asset.register-aset.index', 'roles' => ['admin', 'admin_gudang', 'kepala_unit', 'pegawai']],
            'laporan' => ['route' => 'reports.index', 'roles' => ['admin', 'kepala_pusat', 'admin_gudang', 'kasubbag_tu']],
            'admin' => [
                'route' => null,
                'roles' => ['admin'],
                'submenus' => [
                    'roles' => ['route' => 'admin.roles.index', 'roles' => ['admin']],
                    'users' => ['route' => 'admin.users.index', 'roles' => ['admin']],
                ],
            ],
        ];

        // Filter menus berdasarkan role user
        $accessibleMenus = [];
        $userRoles = $user->roles->pluck('name')->toArray();

        foreach ($menus as $key => $menu) {
            $allowedRoles = $menu['roles'] ?? [];
            
            // Check if user has access
            if (in_array('*', $allowedRoles) || 
                !empty(array_intersect($userRoles, $allowedRoles)) ||
                $user->hasRole('admin')) {
                
                $accessibleMenu = $menu;
                
                // Filter submenus if exists
                if (isset($menu['submenus'])) {
                    $accessibleSubmenus = [];
                    foreach ($menu['submenus'] as $subKey => $submenu) {
                        $subAllowedRoles = $submenu['roles'] ?? [];
                        if (in_array('*', $subAllowedRoles) || 
                            !empty(array_intersect($userRoles, $subAllowedRoles)) ||
                            $user->hasRole('admin')) {
                            $accessibleSubmenus[$subKey] = $submenu;
                        }
                    }
                    $accessibleMenu['submenus'] = $accessibleSubmenus;
                }
                
                $accessibleMenus[$key] = $accessibleMenu;
            }
        }

        return $accessibleMenus;
    }
}

