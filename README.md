# Todo

## Yang Sudah Pada Aplikasi 2
1.  Fondasi Profesional: Aplikasi ini sudah menggunakan Laravel Breeze dengan sistem login dan layout sidebar interaktif.

2.  Sinkronisasi Cerdas:
Menggunakan arsitektur Event-Driven yang modern.
Form dinamis mengambil data dari Aplikasi 1 untuk mencegah kesalahan input.
Berhasil mengirim data Aset dan Laporan Insiden baru ke Aplikasi 1 secara otomatis.

3. Menyempurnakan Sinkronisasi dengan Sistem Antrian (Queue)
Implementasi: Proses sinkronisasi di Aplikasi 2 sekarang berjalan secara asinkron. Tugas pengiriman API "dititipkan" ke dalam antrian di database, dan diproses oleh "pekerja" (php artisan queue:work) di latar belakang.

4. Membangun Dashboard Sinkronisasi di Aplikasi 2
Masalah Saat Ini: Untuk melihat apakah sinkronisasi berhasil atau gagal, kita harus memeriksa file log, yang tidak praktis untuk admin.

Tujuan: Membangun halaman "Log Sinkronisasi" di Aplikasi 2. Halaman ini akan menampilkan tabel riwayat semua upaya pengiriman data ke Aplikasi 1, lengkap dengan statusnya (Berhasil atau Gagal) dan pesan error jika ada.



## Yang Belum

1. Sinkronisasi Dua Arah (Update & Delete)