# Dashboard 3TJ

Aplikasi Point of Sale (POS) ini dirancang untuk memudahkan pengelolaan penjualan dan inventaris dengan fitur-fitur canggih dan user-friendly. Dengan aplikasi ini, Anda dapat mengoptimalkan proses penjualan, memantau stok barang, dan melacak riwayat inventaris secara efisien.

## Persyaratan

- PHP >= 7.4
- Composer
- MySQL

## Instalasi

1. **Kloning repository ini**

    ```bash
    git clone https://github.com/danarreichi/dashboard-3tj.git
    cd dashboard-3tj
    ```

2. **Instal dependensi dengan Composer**

    ```bash
    composer install
    ```

3. **Salin file .env.example ke .env**

    ```bash
    cp .env.example .env
    ```

4. **Konfigurasi file .env**

    Atur pengaturan database dan pengaturan lainnya di file `.env` sesuai kebutuhan Anda.

5. **Generate aplikasi key**

    ```bash
    php artisan key:generate
    ```

6. **Migrasi dan Seed database**

    ```bash
    php artisan migrate --seed
    ```

## Menjalankan Server

1. **Jalankan server lokal**

    ```bash
    php artisan serve
    ```

2. **Akses aplikasi di browser**

    Buka [http://localhost:8000](http://localhost:8000) di browser Anda.

## Fitur Utama

1. **Login dengan Bearer Code**
   - Sistem login yang aman menggunakan bearer code untuk memastikan hanya pengguna terotorisasi yang dapat mengakses aplikasi.

2. **Server-Side Data Loading (AJAX)**
   - Data dimuat secara dinamis dari server menggunakan teknologi AJAX, memastikan performa yang cepat dan responsif tanpa perlu me-refresh halaman.

3. **Melihat Riwayat Inventory Masuk & Keluar**
   - Fitur untuk melacak riwayat barang masuk dan keluar dari inventaris, lengkap dengan informasi pengguna yang melakukan transaksi tersebut, memberikan transparansi penuh dan akurasi dalam manajemen stok.

4. **Tampilan Dinamis**
   - Antarmuka pengguna yang dinamis dan interaktif, dirancang untuk memberikan pengalaman pengguna yang intuitif dan menyenangkan. Tampilan akan berubah secara real-time sesuai dengan aktivitas pengguna.

5. **Tampilan Stok yang Berubah Dinamis**
   - Pilihan menu yang menampilkan stok barang akan otomatis diperbarui secara dinamis, memastikan informasi stok selalu terkini dan akurat. Ini membantu pengguna untuk mengambil keputusan yang tepat berdasarkan ketersediaan barang.

## Catatan & To-Do List
- Aplikasi ini masih dalam tahap pengembangan.
- Akan ada penambahan fitur untuk checkout, diskon, & print struk pembelian.
- Untuk sementara Stock produk dinamis ada masalah _slow query_ jika terdapat banyak data, fitur ini masih berupa _proof of concept_.

## Kontribusi

1. Fork repository ini
2. Buat branch fitur Anda (`git checkout -b fitur/AmazingFeature`)
3. Commit perubahan Anda (`git commit -m 'Tambah fitur AmazingFeature'`)
4. Push ke branch (`git push origin fitur/AmazingFeature`)
5. Buka Pull Request
