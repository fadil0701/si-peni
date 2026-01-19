<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\AssetController;
use App\Http\Controllers\User\RequestController;
use App\Http\Controllers\Master\UnitKerjaController;
use App\Http\Controllers\Master\GudangController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\ProgramController;
use App\Http\Controllers\Master\KegiatanController;
use App\Http\Controllers\Master\SubKegiatanController;
use App\Http\Controllers\Master\AsetController;
use App\Http\Controllers\Master\KodeBarangController;
use App\Http\Controllers\Master\KategoriBarangController;
use App\Http\Controllers\Master\JenisBarangController;
use App\Http\Controllers\Master\SubjenisBarangController;
use App\Http\Controllers\Master\DataBarangController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\SumberAnggaranController;
use App\Http\Controllers\Inventory\DataStockController;
use App\Http\Controllers\Inventory\DataInventoryController;
use App\Http\Controllers\Transaction\DistribusiController;
use App\Http\Controllers\Transaction\PermintaanBarangController;
use App\Http\Controllers\Transaction\PenerimaanBarangController;
use App\Http\Controllers\Asset\RegisterAsetController;
use App\Http\Controllers\Planning\RkuController;
use App\Http\Controllers\Procurement\PaketPengadaanController;
use App\Http\Controllers\Finance\PembayaranController;
use App\Http\Controllers\Report\ReportController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('user.dashboard');
    
    // Assets
    Route::get('/assets', [AssetController::class, 'index'])->name('user.assets');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('user.assets.show');
    
    // Requests
    Route::get('/requests', [RequestController::class, 'index'])->name('user.requests');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('user.requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('user.requests.store');
    Route::get('/requests/{id}', [RequestController::class, 'show'])->name('user.requests.show');
    
    // Master Manajemen
    Route::prefix('master-manajemen')->name('master-manajemen.')->group(function () {
        Route::resource('master-pegawai', \App\Http\Controllers\MasterManajemen\MasterPegawaiController::class);
    });
    
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('unit-kerja', UnitKerjaController::class);
        Route::resource('gudang', GudangController::class);
        Route::resource('ruangan', RuanganController::class);
        Route::resource('program', ProgramController::class);
        Route::resource('kegiatan', KegiatanController::class);
        Route::resource('sub-kegiatan', SubKegiatanController::class);
    });
    
    // Master Data
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('aset', AsetController::class);
        Route::resource('kode-barang', KodeBarangController::class);
        Route::resource('kategori-barang', KategoriBarangController::class);
        Route::resource('jenis-barang', JenisBarangController::class);
        Route::resource('subjenis-barang', SubjenisBarangController::class);
        Route::resource('data-barang', DataBarangController::class);
        Route::resource('satuan', SatuanController::class);
        Route::resource('sumber-anggaran', SumberAnggaranController::class);
    });
    
    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('data-stock', [DataStockController::class, 'index'])->name('data-stock.index');
        Route::resource('data-inventory', DataInventoryController::class);
        Route::resource('inventory-item', \App\Http\Controllers\Inventory\InventoryItemController::class);
    });
    
    // API Routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/gudang/{id}/ruangans', function ($id) {
            $gudang = \App\Models\MasterGudang::with('unitKerja')->findOrFail($id);
            $ruangans = \App\Models\MasterRuangan::where('id_unit_kerja', $gudang->id_unit_kerja)->get();
            return response()->json(['ruangans' => $ruangans]);
        });
        Route::get('/gudang/{id}/inventory', [\App\Http\Controllers\Transaction\DistribusiController::class, 'getInventoryByGudang'])->name('gudang.inventory');
        Route::get('/permintaan/{id}/detail', [\App\Http\Controllers\Transaction\DistribusiController::class, 'getPermintaanDetail'])->name('permintaan.detail');
        Route::get('/distribusi/{id}/detail', [\App\Http\Controllers\Transaction\PenerimaanBarangController::class, 'getDistribusiDetail'])->name('distribusi.detail');
    });
    
    // Transaction
    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::resource('distribusi', DistribusiController::class);
        Route::post('distribusi/{id}/kirim', [DistribusiController::class, 'kirim'])->name('distribusi.kirim');
        Route::resource('permintaan-barang', PermintaanBarangController::class);
        Route::post('permintaan-barang/{id}/ajukan', [PermintaanBarangController::class, 'ajukan'])->name('permintaan-barang.ajukan');
        Route::resource('penerimaan-barang', PenerimaanBarangController::class);
        
        // Approval
        Route::prefix('approval')->name('approval.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Transaction\ApprovalPermintaanController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Transaction\ApprovalPermintaanController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Transaction\ApprovalPermintaanController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Transaction\ApprovalPermintaanController::class, 'reject'])->name('reject');
        });
    });
    
    // Asset & KIR
    Route::prefix('asset')->name('asset.')->group(function () {
        Route::resource('register-aset', RegisterAsetController::class);
    });
    
    // Planning
    Route::prefix('planning')->name('planning.')->group(function () {
        Route::resource('rku', RkuController::class);
    });
    
    // Procurement
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::resource('paket-pengadaan', PaketPengadaanController::class);
    });
    
    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::resource('pembayaran', PembayaranController::class);
    });
    
    // Admin - Role & User Management
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('stock-gudang', [ReportController::class, 'stockGudang'])->name('stock-gudang');
        Route::get('stock-gudang/export', [ReportController::class, 'exportStockGudang'])->name('stock-gudang.export');
    });
});
