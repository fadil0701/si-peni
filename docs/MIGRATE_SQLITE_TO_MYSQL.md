# Panduan Migrasi Data dari SQLite ke MySQL

## 📋 Persiapan

### 1. Konfigurasi MySQL di `.env`

Pastikan MySQL sudah dikonfigurasi di file `.env`:

```env
DB_CONNECTION=sqlite  # Tetap sqlite untuk saat ini (akan diubah setelah migrasi)
DB_DATABASE=database/database.sqlite

# Konfigurasi MySQL (untuk migrasi)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE_MYSQL=si_peni  # Nama database MySQL
DB_USERNAME_MYSQL=root
DB_PASSWORD_MYSQL=
```

**Catatan**: Command ini akan menggunakan konfigurasi MySQL dari `config/database.php` yang membaca dari `.env` dengan prefix `DB_*`.

### 2. Buat Database MySQL

Buat database MySQL terlebih dahulu:

```sql
CREATE DATABASE si_peni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau melalui command line:

```bash
mysql -u root -p -e "CREATE DATABASE si_peni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Update `.env` untuk MySQL

Sementara, update `.env` untuk sementara (akan dikembalikan setelah migrasi):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_peni
DB_USERNAME=root
DB_PASSWORD=
```

## 🚀 Cara Menggunakan

### Opsi 1: Migrasi Lengkap (Schema + Data)

```bash
php artisan migrate:sqlite-to-mysql
```

Command ini akan:
1. ✅ Menjalankan migrations di MySQL (membuat tabel)
2. ✅ Membaca semua data dari SQLite
3. ✅ Memasukkan data ke MySQL dengan urutan yang benar
4. ✅ Menangani foreign key constraints

### Opsi 2: Migrasi dengan Konfirmasi

```bash
php artisan migrate:sqlite-to-mysql
# Akan muncul konfirmasi: "This will migrate all data from SQLite to MySQL. Continue? (yes/no)"
```

### Opsi 3: Migrasi Tanpa Konfirmasi (Force)

```bash
php artisan migrate:sqlite-to-mysql --force
```

### Opsi 4: Hanya Migrasi Schema (Tanpa Data)

```bash
php artisan migrate:sqlite-to-mysql --skip-data
```

## 📊 Proses Migrasi

Command akan melakukan:

1. **Step 1: Running Migrations**
   - Menjalankan semua migrations di MySQL
   - Membuat struktur tabel

2. **Step 2: Reading Tables**
   - Membaca semua tabel dari SQLite
   - Menentukan urutan migrasi (menghindari foreign key errors)

3. **Step 3: Migrating Data**
   - Memigrasikan data tabel per tabel
   - Menangani data types yang berbeda
   - Menampilkan progress untuk setiap tabel

## ⚠️ Catatan Penting

### 1. Backup Data
**PENTING**: Backup database SQLite sebelum migrasi!

```bash
# Backup SQLite
cp database/database.sqlite database/database.sqlite.backup
```

### 2. Foreign Key Constraints
- Command akan menonaktifkan foreign key checks sementara saat migrasi
- Data akan dimigrasikan dalam urutan yang benar untuk menghindari constraint errors

### 3. Auto Increment Values
- Auto increment values akan dipertahankan dari SQLite
- Pastikan tidak ada konflik ID

### 4. Data Types
- Boolean: Dikonversi dari SQLite (0/1) ke MySQL (0/1)
- DateTime: Format SQLite kompatibel dengan MySQL
- NULL values: Ditangani dengan benar

### 5. Tabel Migrations
- Tabel `migrations` akan di-skip (ditangani oleh Laravel migrations)
- Pastikan semua migrations sudah dijalankan di MySQL

## 🔍 Troubleshooting

### Error: "MySQL database not configured"
**Solusi**: Pastikan konfigurasi MySQL di `.env` sudah benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_peni
DB_USERNAME=root
DB_PASSWORD=
```

### Error: "SQLite database not found"
**Solusi**: Pastikan file `database/database.sqlite` ada

### Error: "Table does not exist in MySQL"
**Solusi**: Pastikan migrations sudah dijalankan:
```bash
php artisan migrate --database=mysql
```

### Error: Foreign Key Constraint Failed
**Solusi**: Command sudah menangani ini dengan menonaktifkan foreign key checks sementara. Jika masih error, periksa urutan tabel di method `getTableOrder()`.

### Error: Duplicate Entry
**Solusi**: Uncomment baris `truncate()` di method `migrateTable()` jika ingin menghapus data yang sudah ada di MySQL terlebih dahulu.

## ✅ Setelah Migrasi

### 1. Update `.env`
Setelah migrasi selesai, pastikan `.env` menggunakan MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_peni
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Test Aplikasi
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Lalu test aplikasi untuk memastikan semua berfungsi dengan baik.

### 3. Backup SQLite (Opsional)
Setelah memastikan semua berfungsi, Anda bisa:
- Menyimpan backup SQLite sebagai arsip
- Atau menghapus SQLite jika sudah tidak diperlukan

## 📝 Contoh Output

```
Starting migration from SQLite to MySQL...

Step 1: Running migrations on MySQL...
  Note: This will create/update tables in MySQL database.
✓ Migrations completed

Step 2: Reading tables from SQLite...
✓ Found 45 tables

Step 3: Migrating data...
  Migrating table: roles...
  ✓ Migrated 5 records from roles
  Migrating table: permissions...
  ✓ Migrated 120 records from permissions
  Migrating table: users...
  ✓ Migrated 10 records from users
  ...

✓ Migration completed! Total records migrated: 1523

Next steps:
1. Update .env file: DB_CONNECTION=mysql
2. Test your application
3. Backup SQLite database before removing it
```

## 🎯 Tips

1. **Test di Environment Development Dulu**: Test migrasi di development sebelum production
2. **Backup**: Selalu backup sebelum migrasi
3. **Verifikasi Data**: Setelah migrasi, verifikasi beberapa record penting
4. **Monitor Logs**: Perhatikan output command untuk error atau warning
5. **Incremental Migration**: Jika data sangat besar, pertimbangkan migrasi bertahap

