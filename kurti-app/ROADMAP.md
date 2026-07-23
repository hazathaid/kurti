# Roadmap Penyelesaian Kurti Mobile

Dokumen ini menjadi panduan penyelesaian aplikasi Kurti Mobile menggunakan React Native dan Expo. Fokus utama adalah menyelesaikan MVP dari kode yang sudah ada tanpa menulis ulang aplikasi.

## 1. Tujuan MVP

Aplikasi dapat digunakan oleh dua jenis pengguna:

- **Fasilitator** dapat login, melihat daftar murid, melihat detail Kurti, dan membuat beberapa aktivitas Kurti.
- **Orang tua** dapat login, melihat Kurti anak, membuka detail aktivitas, dan mengisi catatan orang tua.
- Sesi pengguna tetap aktif setelah aplikasi ditutup dan dibuka kembali.
- Pengguna hanya dapat melihat atau mengubah data yang memang menjadi haknya.
- Semua kondisi loading, data kosong, koneksi gagal, validasi, dan token kedaluwarsa ditangani dengan jelas.

## 2. Keputusan Teknis

- Gunakan **Expo + React Native**.
- Gunakan **React Navigation** sebagai satu-satunya sistem navigasi.
- Gunakan `axios` melalui satu API client untuk seluruh request.
- Gunakan `AsyncStorage` untuk menyimpan token dan data pengguna.
- Laravel Sanctum tetap digunakan untuk autentikasi API.
- URL API berasal dari konfigurasi environment, bukan ditulis langsung di setiap layar.
- Jangan menambahkan dependency baru sebelum kebutuhan dan dampaknya disetujui.

## 3. Struktur yang Dituju

```text
kurti-app/
├── App.js
├── src/
│   ├── api/
│   │   └── client.js
│   ├── components/
│   │   ├── EmptyState.js
│   │   ├── ErrorState.js
│   │   └── LoadingScreen.js
│   ├── config/
│   │   └── env.js
│   ├── contexts/
│   │   └── AuthContext.js
│   ├── navigation/
│   │   └── AppNavigator.js
│   └── screens/
│       ├── LoginScreen.js
│       ├── DashboardFasil.js
│       ├── DashboardOrtu.js
│       ├── DetailKurtiScreen.js
│       └── CreateMultipleKurtiScreen.js
└── ROADMAP.md
```

Struktur ini adalah arah akhir, bukan kewajiban membuat semua file sekaligus. Buat komponen baru hanya ketika benar-benar dipakai ulang.

## 4. Urutan Implementasi

### Fase 1 — Stabilkan Backend dan Kontrak API

- [ ] Ubah helper dashboard fasilitator agar mengembalikan array/data biasa, bukan `response()->json()` di dalam respons lain.
- [ ] Pastikan respons `GET /api/dashboard` konsisten untuk kedua tipe pengguna.
- [ ] Tambahkan validasi `bulan` dan `pekan` saat membuat Kurti.
- [ ] Tambahkan authorization pada detail, pembuatan, dan pembaruan Kurti.
- [ ] Orang tua hanya boleh melihat Kurti anak yang terhubung dengannya.
- [ ] Orang tua hanya boleh mengubah `catatan_orang_tua` milik anaknya.
- [ ] Fasilitator hanya boleh membuat dan melihat Kurti murid di kelas aktifnya.
- [ ] Kembalikan status HTTP yang tepat: `401`, `403`, `404`, dan `422`.
- [ ] Hapus atau lindungi endpoint `/api/test-push` sebelum production.
- [ ] Jangan menyimpan Expo push token contoh di source code.

Format respons sukses yang disarankan:

```json
{
  "status": "success",
  "data": {},
  "message": "Optional message"
}
```

Format respons gagal yang disarankan:

```json
{
  "status": "error",
  "message": "Pesan yang dapat dipahami pengguna",
  "errors": {}
}
```

**Acceptance criteria:** aplikasi tidak perlu membaca data melalui `json.data.original.data`, dan percobaan mengakses ID milik pengguna lain menghasilkan `403` atau `404`.

### Fase 2 — Rapikan Fondasi Mobile

- [ ] Pertahankan React Navigation dan hapus sisa template Expo Router yang tidak digunakan.
- [ ] Hapus `DashboardScreen.js` dan navigator lama jika sudah dipastikan tidak dipakai.
- [ ] Simpan base URL pada satu konfigurasi environment.
- [ ] Perbaiki base URL agar tidak memakai `//api`.
- [ ] Gunakan `src/api/client.js` untuk semua request.
- [ ] Tambahkan interceptor response untuk menangani token kedaluwarsa.
- [ ] Jangan mencetak token atau data sensitif ke log.
- [ ] Gunakan penamaan field yang konsisten: database `catatan_orang_tua`, payload API `catatan_orangtua` atau satu nama lain yang disepakati.

**Acceptance criteria:** tidak ada URL API hardcoded di layar dan hanya ada satu sistem navigasi aktif.

### Fase 3 — Autentikasi dan Sesi

- [ ] Saat login berhasil, simpan token dan data minimum pengguna.
- [ ] Saat aplikasi dibuka, pulihkan sesi dari `AsyncStorage`.
- [ ] Tampilkan loading screen selama pemeriksaan sesi.
- [ ] Saat logout, panggil `POST /api/logout`.
- [ ] Hapus token dan data pengguna lokal walaupun request logout server gagal.
- [ ] Jika API mengembalikan `401`, hapus sesi dan arahkan pengguna ke login.
- [ ] Normalisasi email dengan `trim()` dan lowercase sebelum login.
- [ ] Tampilkan pesan validasi dari status `422` dan pesan kredensial salah dari `401`.
- [ ] Hapus atau nonaktifkan tombol lupa password sampai fiturnya tersedia.

**Acceptance criteria:** pengguna tidak perlu login kembali setelah aplikasi ditutup, tetapi otomatis kembali ke login jika token tidak valid.

### Fase 4 — Dashboard

#### Dashboard Orang Tua

- [ ] Tampilkan nama setiap anak.
- [ ] Kelompokkan data berdasarkan bulan dan pekan.
- [ ] Tampilkan jumlah aktivitas yang sudah dan belum diisi.
- [ ] Gunakan status yang mudah dipahami: `Belum diisi`, `Sedang diisi`, dan `Selesai`.
- [ ] Sediakan pull-to-refresh.
- [ ] Tampilkan empty state jika belum ada data.
- [ ] Tampilkan tombol coba lagi jika request gagal.

#### Dashboard Fasilitator

- [ ] Tampilkan kelas aktif dan daftar murid.
- [ ] Tampilkan kelompok bulan dan pekan untuk setiap murid.
- [ ] Tampilkan jumlah aktivitas pada setiap kelompok.
- [ ] Tombol tambah Kurti membawa `muridId` dan `classroomId` yang benar.
- [ ] Dashboard refresh setelah kembali dari layar tambah Kurti.
- [ ] Sediakan pull-to-refresh, empty state, dan retry state.

**Acceptance criteria:** data terbaru muncul tanpa logout/login dan error jaringan tidak menghasilkan layar kosong tanpa penjelasan.

### Fase 5 — Detail dan Catatan Kurti

- [ ] Tampilkan identitas murid, bulan, dan pekan.
- [ ] Tampilkan semua aktivitas, amanah rumah, capaian, dan catatan orang tua.
- [ ] Fasilitator hanya dapat membaca catatan orang tua.
- [ ] Orang tua dapat mengubah dan menyimpan catatan.
- [ ] Gunakan token dari Auth Context/API client, bukan mengambilnya ulang di layar.
- [ ] Tombol simpan hanya loading untuk item yang sedang disimpan.
- [ ] Tampilkan pesan sukses setelah server benar-benar menyimpan perubahan.
- [ ] Pertahankan input jika request gagal agar tulisan pengguna tidak hilang.
- [ ] Tangani detail yang sudah dihapus atau tidak boleh diakses.

**Acceptance criteria:** catatan yang disimpan tetap tampil setelah halaman dimuat ulang dan tidak dapat diubah oleh fasilitator.

### Fase 6 — Pembuatan Kurti

- [ ] Pilih bulan dengan format yang konsisten, misalnya `YYYY-MM`.
- [ ] Batasi pekan ke nilai yang valid sesuai aturan sekolah.
- [ ] Aktivitas wajib diisi.
- [ ] Cegah pengiriman ganda ketika tombol simpan ditekan berulang.
- [ ] Tampilkan error validasi per baris.
- [ ] Konfirmasi sebelum menghapus baris yang sudah terisi.
- [ ] Setelah sukses, kembali dan refresh dashboard.
- [ ] Pertimbangkan satu bulan dan pekan untuk beberapa aktivitas agar pengguna tidak mengisi data berulang.

**Acceptance criteria:** semua baris valid tersimpan dalam satu aksi dan kegagalan validasi menunjukkan baris serta field yang bermasalah.

### Fase 7 — Notifikasi

- [ ] Minta izin notifikasi pada waktu yang tepat.
- [ ] Simpan Expo push token melalui endpoint yang terautentikasi.
- [ ] Perbarui token jika token perangkat berubah.
- [ ] Tentukan peristiwa notifikasi, misalnya Kurti baru dibuat atau catatan orang tua diisi.
- [ ] Menekan notifikasi membuka layar yang sesuai jika datanya masih tersedia.
- [ ] Kegagalan registrasi notifikasi tidak boleh menghalangi penggunaan fitur utama.

Notifikasi bukan blocker MVP apabila login, dashboard, detail, dan pengisian data sudah berfungsi.

### Fase 8 — UI dan Aksesibilitas

- [ ] Gunakan warna, spacing, radius, dan typography yang konsisten.
- [ ] Pastikan tombol memiliki area tekan yang cukup.
- [ ] Pastikan teks tetap terbaca saat ukuran font perangkat diperbesar.
- [ ] Gunakan keyboard yang sesuai untuk email dan angka.
- [ ] Hindari informasi penting yang hanya dibedakan dengan warna.
- [ ] Pastikan layout dapat digunakan pada layar Android kecil.
- [ ] Tambahkan konfirmasi jika pengguna keluar dari form dengan perubahan yang belum disimpan.

## 5. Pengujian Minimum

### Backend

- [ ] Login berhasil dan gagal.
- [ ] Logout mencabut token aktif.
- [ ] Dashboard orang tua hanya mengembalikan anak miliknya.
- [ ] Dashboard fasilitator hanya mengembalikan kelas aktifnya.
- [ ] Orang tua tidak dapat melihat atau mengubah Kurti anak lain.
- [ ] Fasilitator tidak dapat membuat Kurti untuk kelas lain.
- [ ] Validasi pembuatan beberapa Kurti.
- [ ] Pembaruan catatan tersimpan dengan nama field yang benar.

### Mobile

- [ ] Login sebagai orang tua.
- [ ] Login sebagai fasilitator.
- [ ] Pemulihan sesi setelah restart aplikasi.
- [ ] Logout dan token kedaluwarsa.
- [ ] Dashboard loading, sukses, kosong, dan gagal.
- [ ] Refresh dashboard.
- [ ] Buka detail Kurti.
- [ ] Simpan catatan orang tua.
- [ ] Buat satu dan beberapa aktivitas Kurti.
- [ ] Uji tanpa koneksi dan dengan koneksi lambat.
- [ ] Uji minimal pada satu perangkat Android fisik.

## 6. Definition of Done

MVP dianggap selesai jika:

- [ ] Seluruh acceptance criteria Fase 1–6 terpenuhi.
- [ ] Tidak ada endpoint sensitif tanpa authorization.
- [ ] Tidak ada URL production yang tersebar di layar.
- [ ] Tidak ada error atau warning penting saat lint/build.
- [ ] Test backend utama lulus.
- [ ] Alur utama lulus pada perangkat Android fisik.
- [ ] Tidak ada data pengguna lain yang dapat diakses dengan mengganti ID request.
- [ ] Konfigurasi development dan production terdokumentasi.
- [ ] Folder `kurti-app` sudah masuk version control dengan `.gitignore` yang benar.
- [ ] Build production dapat dipasang dan menjalankan login sampai penyimpanan catatan.

## 7. Urutan Pengerjaan Praktis

Kerjakan dalam urutan berikut agar perubahan mudah diuji:

1. Benahi authorization dan bentuk respons API.
2. Satukan konfigurasi serta API client mobile.
3. Selesaikan persistensi login dan logout.
4. Benahi dashboard orang tua.
5. Benahi dashboard fasilitator.
6. Benahi detail dan penyimpanan catatan.
7. Benahi form pembuatan Kurti.
8. Tambahkan test dan lakukan pengujian perangkat.
9. Rapikan UI.
10. Aktifkan notifikasi setelah alur utama stabil.

Setiap langkah sebaiknya dibuat dalam commit kecil yang dapat diuji dan dikembalikan secara terpisah.

## 8. Hal yang Tidak Perlu Dikerjakan Sekarang

- Migrasi ke Flutter.
- State management tambahan seperti Redux jika Context masih mencukupi.
- Offline synchronization penuh.
- Animasi kompleks.
- Dukungan web dari aplikasi Expo.
- Refactor besar yang tidak memperbaiki alur pengguna atau keamanan.

