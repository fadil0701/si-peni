<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DataInventory;
use App\Models\InventoryItem;
use App\Models\DataStock;
use App\Models\MasterDataBarang;
use App\Models\MasterGudang;
use App\Models\MasterSumberAnggaran;
use App\Models\MasterSubKegiatan;
use App\Models\MasterSatuan;
use App\Models\MasterUnitKerja;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DataInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DataInventory::with(['dataBarang', 'gudang', 'sumberAnggaran', 'subKegiatan', 'satuan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('dataBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_data_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_inventory')) {
            $query->where('jenis_inventory', $request->jenis_inventory);
        }

        if ($request->filled('gudang')) {
            $query->where('id_gudang', $request->gudang);
        }

        $inventories = $query->latest()->paginate(15);
        $gudangs = MasterGudang::all();
        $dataBarangs = MasterDataBarang::all();

        return view('inventory.data-inventory.index', compact('inventories', 'gudangs', 'dataBarangs'));
    }

    public function create()
    {
        $dataBarangs = MasterDataBarang::all();
        // Hanya tampilkan gudang PUSAT untuk input inventory
        $gudangs = MasterGudang::where('jenis_gudang', 'PUSAT')->get();
        $sumberAnggarans = MasterSumberAnggaran::all();
        $subKegiatans = MasterSubKegiatan::all();
        $satuans = MasterSatuan::all();
        $unitKerjas = MasterUnitKerja::all();

        return view('inventory.data-inventory.create', compact(
            'dataBarangs', 'gudangs', 'sumberAnggarans', 'subKegiatans', 'satuans', 'unitKerjas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
            'id_gudang' => [
                'required',
                'exists:master_gudang,id_gudang',
                function ($attribute, $value, $fail) {
                    $gudang = MasterGudang::find($value);
                    if ($gudang && $gudang->jenis_gudang !== 'PUSAT') {
                        $fail('Data inventory hanya dapat disimpan di gudang PUSAT. Gudang UNIT hanya menerima distribusi barang.');
                    }
                },
            ],
            'id_anggaran' => 'required|exists:master_sumber_anggaran,id_anggaran',
            'id_sub_kegiatan' => 'required|exists:master_sub_kegiatan,id_sub_kegiatan',
            'jenis_inventory' => 'required|in:ASET,PERSEDIAAN,FARMASI',
            'tahun_anggaran' => 'required|integer|min:2000|max:2100',
            'qty_input' => 'required|numeric|min:1',
            'id_satuan' => 'required|exists:master_satuan,id_satuan',
            'harga_satuan' => 'required|numeric|min:0',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'spesifikasi' => 'nullable|string',
            'tahun_produksi' => 'nullable|integer',
            'nama_penyedia' => 'nullable|string|max:255',
            'no_seri' => 'nullable|string|max:255',
            'no_batch' => 'nullable|string|max:255',
            'tanggal_kedaluwarsa' => 'nullable|date',
            'status_inventory' => 'required|in:DRAFT,AKTIF,DISTRIBUSI,HABIS',
            'upload_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $validated['total_harga'] = $validated['qty_input'] * $validated['harga_satuan'];
        $validated['created_by'] = auth()->id();

        // Handle file uploads
        if ($request->hasFile('upload_foto')) {
            $validated['upload_foto'] = $request->file('upload_foto')->store('foto-inventory', 'public');
        }

        if ($request->hasFile('upload_dokumen')) {
            $validated['upload_dokumen'] = $request->file('upload_dokumen')->store('dokumen-inventory', 'public');
        }

        DB::beginTransaction();
        try {
            // Insert ke data_inventory
            $inventory = DataInventory::create($validated);

            // Auto Register Aset jika jenis = ASET
            if ($validated['jenis_inventory'] === 'ASET' && $validated['qty_input'] > 0) {
                $this->autoRegisterAset($inventory, $validated);
            }

            // Update atau create data_stock
            $this->updateStock($inventory, $validated);

            DB::commit();

            return redirect()->route('inventory.data-inventory.index')
                ->with('success', 'Data Inventory berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Error saving DataInventory: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan periksa kembali data yang diinput.');
        }
    }

    private function autoRegisterAset(DataInventory $inventory, array $data)
    {
        $dataBarang = $inventory->dataBarang;
        $gudang = $inventory->gudang;
        $unitKerja = $gudang->unitKerja ?? MasterUnitKerja::first();
        
        // Generate kode register base - ambil dari hierarki barang
        $kodeBarang = 'UNK';
        try {
            if ($dataBarang && $dataBarang->subjenisBarang) {
                $subjenis = $dataBarang->subjenisBarang;
                if ($subjenis->jenisBarang && $subjenis->jenisBarang->kategoriBarang) {
                    $kategori = $subjenis->jenisBarang->kategoriBarang;
                    if ($kategori->kodeBarang) {
                        $kodeBarang = $kategori->kodeBarang->kode_barang;
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback jika relasi tidak lengkap
            $kodeBarang = 'UNK';
        }
        
        $tahun = $data['tahun_anggaran'];
        $unitCode = $unitKerja ? $unitKerja->kode_unit_kerja : 'UNIT';

        // Get max urut untuk tahun dan kode barang ini
        $existingRegisters = InventoryItem::where('kode_register', 'like', "{$unitCode}/{$kodeBarang}/{$tahun}/%")
            ->get()
            ->map(function ($item) {
                $parts = explode('/', $item->kode_register);
                return isset($parts[3]) ? (int)$parts[3] : 0;
            });
        
        $maxUrut = $existingRegisters->max() ?? 0;

        // Loop untuk setiap qty
        for ($i = 1; $i <= $data['qty_input']; $i++) {
            $urut = $maxUrut + $i;
            $kodeRegister = sprintf('%s/%s/%s/%04d', $unitCode, $kodeBarang, $tahun, $urut);

            // Generate QR Code
            // Buat struktur direktori berdasarkan kode register: unit/kode/tahun/
            $pathParts = explode('/', $kodeRegister);
            $baseDir = storage_path('app/public/qrcodes/inventory_item');
            
            // Buat direktori secara rekursif berdasarkan struktur path
            $currentDir = $baseDir;
            for ($j = 0; $j < count($pathParts) - 1; $j++) {
                $currentDir .= DIRECTORY_SEPARATOR . $pathParts[$j];
                if (!file_exists($currentDir)) {
                    if (!mkdir($currentDir, 0755, true) && !is_dir($currentDir)) {
                        throw new \RuntimeException('Directory tidak dapat dibuat: ' . $currentDir);
                    }
                }
            }
            
            // Nama file adalah bagian terakhir dari kode register
            $qrCodeFileName = end($pathParts) . '.svg';
            $qrCodePath = 'qrcodes/inventory_item/' . $kodeRegister . '.svg';
            $fullPath = $currentDir . DIRECTORY_SEPARATOR . $qrCodeFileName;
            
            // Generate QR code and save to storage
            try {
                QrCode::format('svg')->size(200)->generate($kodeRegister, $fullPath);
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed: ' . $e->getMessage(), [
                    'kode_register' => $kodeRegister,
                    'full_path' => $fullPath,
                    'error' => $e->getMessage()
                ]);
                // Jika gagal generate QR code, set path ke null atau empty
                $qrCodePath = null;
            }

            InventoryItem::create([
                'id_inventory' => $inventory->id_inventory,
                'kode_register' => $kodeRegister,
                'no_seri' => $data['no_seri'] ?? null,
                'kondisi_item' => 'BAIK',
                'status_item' => 'AKTIF',
                'id_gudang' => $inventory->id_gudang,
                'id_ruangan' => null,
                'qr_code' => $qrCodePath,
            ]);
        }
    }

    private function updateStock(DataInventory $inventory, array $data)
    {
        $stock = DataStock::firstOrNew([
            'id_data_barang' => $inventory->id_data_barang,
            'id_gudang' => $inventory->id_gudang,
        ]);

        if ($stock->exists) {
            $stock->qty_masuk += $data['qty_input'];
            $stock->qty_akhir += $data['qty_input'];
        } else {
            $stock->qty_awal = 0;
            $stock->qty_masuk = $data['qty_input'];
            $stock->qty_keluar = 0;
            $stock->qty_akhir = $data['qty_input'];
            $stock->id_satuan = $data['id_satuan'];
        }

        $stock->last_updated = now();
        $stock->save();
    }

    public function show($id)
    {
        $inventory = DataInventory::with(['dataBarang', 'gudang', 'sumberAnggaran', 'subKegiatan', 'satuan', 'inventoryItems'])
            ->findOrFail($id);
        return view('inventory.data-inventory.show', compact('inventory'));
    }

    public function edit($id)
    {
        $dataInventory = DataInventory::findOrFail($id);
        
        $dataBarangs = MasterDataBarang::all();
        // Hanya tampilkan gudang PUSAT untuk edit inventory
        $gudangs = MasterGudang::where('jenis_gudang', 'PUSAT')->get();
        $sumberAnggarans = MasterSumberAnggaran::all();
        $subKegiatans = MasterSubKegiatan::all();
        $satuans = MasterSatuan::all();
        $unitKerjas = MasterUnitKerja::all();

        return view('inventory.data-inventory.edit', compact(
            'dataInventory', 'dataBarangs', 'gudangs', 'sumberAnggarans', 'subKegiatans', 'satuans', 'unitKerjas'
        ));
    }

    public function update(Request $request, $id)
    {
        $inventory = DataInventory::findOrFail($id);

        $validated = $request->validate([
            'id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
            'id_gudang' => [
                'required',
                'exists:master_gudang,id_gudang',
                function ($attribute, $value, $fail) {
                    $gudang = MasterGudang::find($value);
                    if ($gudang && $gudang->jenis_gudang !== 'PUSAT') {
                        $fail('Data inventory hanya dapat disimpan di gudang PUSAT. Gudang UNIT hanya menerima distribusi barang.');
                    }
                },
            ],
            'id_anggaran' => 'required|exists:master_sumber_anggaran,id_anggaran',
            'id_sub_kegiatan' => 'required|exists:master_sub_kegiatan,id_sub_kegiatan',
            'jenis_inventory' => 'required|in:ASET,PERSEDIAAN,FARMASI',
            'tahun_anggaran' => 'required|integer|min:2000|max:2100',
            'qty_input' => 'required|numeric|min:1',
            'id_satuan' => 'required|exists:master_satuan,id_satuan',
            'harga_satuan' => 'required|numeric|min:0',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'spesifikasi' => 'nullable|string',
            'tahun_produksi' => 'nullable|integer',
            'nama_penyedia' => 'nullable|string|max:255',
            'no_seri' => 'nullable|string|max:255',
            'no_batch' => 'nullable|string|max:255',
            'tanggal_kedaluwarsa' => 'nullable|date',
            'status_inventory' => 'required|in:DRAFT,AKTIF,DISTRIBUSI,HABIS',
            'upload_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $validated['total_harga'] = $validated['qty_input'] * $validated['harga_satuan'];

        // Handle file uploads
        if ($request->hasFile('upload_foto')) {
            // Hapus foto lama jika ada
            if ($inventory->upload_foto) {
                Storage::disk('public')->delete($inventory->upload_foto);
            }
            $validated['upload_foto'] = $request->file('upload_foto')->store('foto-inventory', 'public');
        }

        if ($request->hasFile('upload_dokumen')) {
            // Hapus dokumen lama jika ada
            if ($inventory->upload_dokumen) {
                Storage::disk('public')->delete($inventory->upload_dokumen);
            }
            $validated['upload_dokumen'] = $request->file('upload_dokumen')->store('dokumen-inventory', 'public');
        }

        $inventory->update($validated);

        return redirect()->route('inventory.data-inventory.index')
            ->with('success', 'Data Inventory berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $inventory = DataInventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory.data-inventory.index')
            ->with('success', 'Data Inventory berhasil dihapus.');
    }
}
