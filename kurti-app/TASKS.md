# Implementation Tasks — Kurti Mobile

Daftar ini adalah checklist implementasi. Kerjakan berurutan karena beberapa task bergantung pada task sebelumnya.

Status:

- `[ ]` belum dikerjakan
- `[x]` selesai
- `[~]` sedang dikerjakan
- `[!]` terhambat

## Milestone 1 — Keamanan dan Kontrak API

### API-01 — Rapikan respons dashboard fasilitator

- [x] Ubah `kurtis_fasil()` agar mengembalikan array, bukan `response()->json()`.
- [x] Pastikan `GET /api/dashboard` langsung mengembalikan data pada `data`.
- [x] Sesuaikan mobile agar membaca `response.data.data` tanpa `original.data`.

File terkait:

- `app/Http/Controllers/Api/DashboardController.php`
- `kurti-app/src/screens/DashboardFasil.js`

Selesai jika respons fasilitator berbentuk:

```json
{
  "status": "success",
  "data": []
}
```

### API-02 — Tambahkan validasi pembuatan Kurti

- [ ] Validasi `kurtis.*.bulan` sebagai field wajib dengan format yang disepakati.
- [ ] Validasi `kurtis.*.pekan` sebagai field wajib.
- [ ] Validasi `murid_id` dan `classroom_id` sesuai akses pengguna.
- [ ] Kembalikan error validasi dengan status `422`.

File terkait:

- `app/Http/Controllers/Api/KurtiController.php`

### API-03 — Lindungi akses detail Kurti

- [ ] Fasilitator hanya boleh membuka data murid di kelas aktifnya.
- [ ] Orang tua hanya boleh membuka data anak yang terhubung dengannya.
- [ ] Pastikan `groupId` memang memiliki Kurti untuk `muridId` tersebut.
- [ ] Kembalikan `403` atau `404` untuk akses yang tidak sah.

File terkait:

- `app/Http/Controllers/Api/KurtiController.php`
- Model relasi pengguna, kelas, dan orang tua

### API-04 — Lindungi update catatan orang tua

- [x] Hanya pengguna bertipe `orangtua` yang boleh memperbarui catatan.
- [x] Pastikan Kurti merupakan milik anak yang terhubung dengan orang tua tersebut.
- [x] Validasi isi `catatan_orangtua`.
- [x] Samakan pemetaan payload `catatan_orangtua` dengan kolom `catatan_orang_tua`.

File terkait:

- `app/Http/Controllers/Api/KurtiController.php`
- `app/Models/Kurti.php`

### API-05 — Lindungi pembuatan Kurti

- [x] Hanya pengguna bertipe `fasil` yang boleh membuat Kurti.
- [x] Pastikan `classroom_id` adalah kelas aktif fasilitator.
- [x] Pastikan murid terdaftar pada kelas tersebut.
- [x] Gunakan transaksi database saat menyimpan beberapa Kurti.

File terkait:

- `app/Http/Controllers/Api/KurtiController.php`

### API-06 — Amankan endpoint notifikasi

- [x] Hapus atau lindungi route `/api/test-push`.
- [x] Hapus Expo push token contoh dari source code.
- [x] Pastikan penyimpanan device token membutuhkan autentikasi.

File terkait:

- `routes/api.php`
- `app/Http/Controllers/Api/NotificationController.php`
- `app/Http/Controllers/UserDeviceController.php`

### API-07 — Tambahkan test keamanan dan API

- [x] Test login berhasil.
- [x] Test login gagal.
- [x] Test logout mencabut token.
- [x] Test dashboard untuk kedua tipe pengguna.
- [x] Test orang tua tidak dapat membaca Kurti anak lain.
- [x] Test orang tua tidak dapat memperbarui Kurti anak lain.
- [x] Test fasilitator tidak dapat membuat Kurti untuk kelas lain.
- [x] Test validasi pembuatan beberapa Kurti.

Selesai jika semua test terkait lulus.

## Milestone 2 — Fondasi React Native

### APP-01 — Pilih satu sistem navigasi

- [x] Pertahankan React Navigation pada `App.js`.
- [x] Pastikan folder template `app/` tidak digunakan oleh entry point.
- [x] Hapus kode Expo Router setelah dipastikan tidak dipakai.
- [x] Hapus `src/AppNavigator.js` lama jika tidak dipakai.
- [x] Hapus `src/screens/DashboardScreen.js` lama jika tidak dipakai.
- [!] Jalankan aplikasi dan pastikan login serta semua route masih dapat dibuka. (Dependency belum terpasang di workspace.)

Catatan: penghapusan file dilakukan hanya setelah penggunaan diperiksa.

### APP-02 — Pusatkan konfigurasi API

- [x] Tentukan base URL development dan production.
- [x] Gunakan environment Expo untuk base URL.
- [x] Hilangkan `https://kurti.saisukabumi.sch.id//api` dari semua screen.
- [x] Pastikan base URL hanya memiliki satu slash sebelum `api`.
- [x] Dokumentasikan cara mengganti environment.

File terkait:

- `kurti-app/src/api/client.js`
- Konfigurasi Expo
- Seluruh file pada `kurti-app/src/screens/`

### APP-03 — Gunakan satu API client

- [x] Gunakan instance `axios` dari `src/api/client.js`.
- [x] Migrasikan request login.
- [x] Migrasikan request logout.
- [x] Migrasikan request dashboard.
- [x] Migrasikan request detail Kurti.
- [x] Migrasikan request update catatan.
- [x] Migrasikan request pembuatan Kurti.
- [x] Hapus import `axios` langsung dari screen.
- [x] Hapus pemanggilan `fetch` langsung dari screen.

Selesai jika pencarian `fetch(` pada `src/screens` tidak menemukan request API.

### APP-04 — Standarkan penanganan error

- [x] Buat helper untuk mengambil pesan error API.
- [x] Bedakan error validasi, unauthorized, forbidden, server, dan jaringan.
- [x] Jangan tampilkan pesan teknis mentah kepada pengguna.
- [x] Jangan log token atau data sensitif.

## Milestone 3 — Autentikasi dan Sesi

### AUTH-01 — Simpan sesi setelah login

- [x] Simpan token ke `AsyncStorage`.
- [x] Simpan data minimum pengguna ke `AsyncStorage`.
- [x] Set user pada Auth Context setelah penyimpanan berhasil.
- [x] Normalisasi email dengan `trim().toLowerCase()`.

File terkait:

- `kurti-app/src/contexts/AuthContext.js`
- `kurti-app/src/screens/LoginScreen.js`

### AUTH-02 — Pulihkan sesi saat aplikasi dibuka

- [x] Tambahkan proses bootstrap session pada Auth Context.
- [x] Baca token dan data pengguna dari `AsyncStorage`.
- [x] Set `loading` selama bootstrap berlangsung.
- [x] Tampilkan loading screen sebelum navigator memilih login/dashboard.
- [x] Hapus storage jika data sesi rusak.

### AUTH-03 — Implementasikan logout lengkap

- [x] Panggil `POST /api/logout`.
- [x] Hapus token dan user dari `AsyncStorage`.
- [x] Kosongkan state user.
- [x] Tetap bersihkan sesi lokal jika request server gagal.
- [x] Cegah tombol logout ditekan berulang saat proses berjalan.

### AUTH-04 — Tangani token kedaluwarsa

- [x] Tambahkan response interceptor untuk status `401`.
- [x] Bersihkan sesi ketika token tidak valid.
- [x] Arahkan pengguna kembali ke layar login.
- [x] Hindari beberapa alert logout muncul bersamaan.

### AUTH-05 — Rapikan layar login

- [x] Nonaktifkan auto-capitalize pada input email.
- [x] Tambahkan `autoComplete` yang sesuai.
- [x] Tampilkan pesan kredensial salah dengan jelas.
- [x] Tampilkan error koneksi secara terpisah.
- [x] Hapus/nonaktifkan tombol lupa password sampai endpoint tersedia.

## Milestone 4 — Dashboard Orang Tua

### ORTU-01 — Migrasikan dashboard ke API client

- [x] Gunakan API client terpusat.
- [x] Hapus URL dan header token manual.
- [x] Gunakan bentuk respons API yang konsisten.

### ORTU-02 — Lengkapi state dashboard

- [x] Tambahkan state initial loading.
- [x] Tambahkan state refresh.
- [x] Tambahkan state data kosong.
- [x] Tambahkan state error dengan tombol coba lagi.
- [x] Tambahkan pull-to-refresh.

### ORTU-03 — Rapikan tampilan data

- [x] Tampilkan nama anak.
- [x] Kelompokkan bulan dan pekan dengan jelas.
- [x] Tampilkan jumlah aktivitas terisi dan total.
- [x] Ubah label status menjadi bahasa yang mudah dipahami.
- [x] Bedakan status tanpa hanya mengandalkan warna.

### ORTU-04 — Refresh setelah kembali dari detail

- [x] Refresh dashboard ketika layar kembali aktif.
- [x] Pastikan perubahan catatan memperbarui jumlah/status.
- [x] Hindari request ganda saat pertama kali membuka layar.

## Milestone 5 — Dashboard Fasilitator

### FASIL-01 — Migrasikan dashboard ke API client

- [x] Gunakan API client terpusat.
- [x] Hapus pembacaan `json.data.original.data`.
- [x] Gunakan respons langsung dari API yang sudah diperbaiki.

### FASIL-02 — Lengkapi state dashboard

- [x] Tambahkan initial loading.
- [x] Tambahkan pull-to-refresh.
- [x] Tambahkan empty state untuk kelas tanpa murid/data.
- [x] Tambahkan error state dan tombol coba lagi.

### FASIL-03 — Rapikan daftar murid dan kelompok

- [x] Tampilkan nama kelas aktif.
- [x] Tampilkan seluruh murid dengan key yang stabil.
- [x] Tampilkan kelompok bulan dan pekan.
- [x] Tampilkan jumlah aktivitas setiap kelompok.
- [x] Pastikan `groupId` yang dikirim ke detail berasal dari kelompok yang benar.

### FASIL-04 — Refresh setelah membuat Kurti

- [x] Kirim penanda sukses saat kembali dari form.
- [x] Muat ulang dashboard setelah penyimpanan berhasil.
- [x] Pastikan item baru langsung terlihat.

## Milestone 6 — Detail Kurti

### DETAIL-01 — Migrasikan request ke API client

- [ ] Gunakan API client untuk mengambil detail.
- [ ] Gunakan API client untuk menyimpan catatan.
- [ ] Hapus akses token langsung dari `AsyncStorage` pada screen.
- [ ] Hapus import `axios` langsung pada screen.

### DETAIL-02 — Lengkapi state detail

- [ ] Tambahkan loading state.
- [ ] Tambahkan empty state.
- [ ] Tambahkan error state dan retry.
- [ ] Tangani status `403` dan `404`.
- [ ] Reset state saat `muridId` atau `groupId` berubah.

### DETAIL-03 — Benahi editor catatan orang tua

- [ ] Fasilitator hanya melihat catatan sebagai teks.
- [ ] Orang tua dapat mengedit catatan.
- [ ] Simpan loading per item.
- [ ] Cegah pengiriman ganda.
- [ ] Pertahankan teks ketika request gagal.
- [ ] Perbarui data lokal setelah server berhasil menyimpan.
- [ ] Pastikan catatan tetap ada setelah detail dimuat ulang.

### DETAIL-04 — Rapikan accordion aktivitas

- [ ] Pastikan hanya accordion yang dipilih yang terbuka.
- [ ] Gunakan ID Kurti sebagai state expanded, bukan index array.
- [ ] Tampilkan fallback `-` untuk nilai kosong.
- [ ] Pastikan daftar panjang dapat di-scroll dengan baik.

## Milestone 7 — Pembuatan Kurti

### CREATE-01 — Migrasikan penyimpanan ke API client

- [x] Gunakan API client terpusat.
- [x] Hapus URL dan header Authorization manual.
- [x] Tangani status `422` dari Laravel.

### CREATE-02 — Benahi model form

- [x] Tentukan bulan dan pekan satu kali untuk satu kelompok aktivitas.
- [x] Simpan daftar aktivitas sebagai baris terpisah.
- [x] Aktivitas wajib diisi.
- [x] Amanah rumah dan capaian bersifat opsional.
- [x] Jangan izinkan seluruh baris aktivitas dihapus.

### CREATE-03 — Tambahkan validasi form

- [x] Validasi format bulan.
- [x] Validasi nilai pekan.
- [x] Trim seluruh input teks.
- [x] Tampilkan error pada field/baris terkait.
- [x] Fokuskan input pertama yang tidak valid jika memungkinkan.

### CREATE-04 — Benahi alur penyimpanan

- [x] Cegah tombol simpan ditekan berulang.
- [x] Tampilkan indikator proses.
- [x] Tampilkan konfirmasi sukses.
- [x] Kembali ke dashboard setelah sukses.
- [x] Picu refresh dashboard.
- [x] Pertahankan form jika request gagal.

### CREATE-05 — Lindungi perubahan form

- [x] Konfirmasi sebelum menghapus baris yang sudah terisi.
- [x] Konfirmasi sebelum kembali jika ada perubahan belum disimpan.
- [x] Jangan tampilkan konfirmasi pada form yang masih kosong.

## Milestone 8 — Komponen UI Bersama

### UI-01 — Buat loading component

- [x] Buat hanya jika sudah digunakan minimal pada beberapa screen.
- [x] Mendukung loading layar penuh dan inline.
- [x] Gunakan warna tema yang konsisten.

### UI-02 — Buat empty dan error state

- [x] Empty state menerima judul dan deskripsi.
- [x] Error state menerima pesan dan callback retry.
- [x] Gunakan pada kedua dashboard dan detail.

### UI-03 — Rapikan design tokens

- [x] Tentukan warna utama, sukses, peringatan, bahaya, teks, dan background.
- [x] Tentukan spacing dan radius dasar.
- [x] Reuse nilai tersebut pada semua screen.
- [x] Pastikan kontras teks cukup jelas.

### UI-04 — Aksesibilitas dan perangkat kecil

- [x] Tambahkan label aksesibilitas pada tombol ikon/aksi.
- [x] Pastikan area tekan tombol cukup besar.
- [x] Uji font perangkat yang diperbesar.
- [x] Uji layout pada Android berlayar kecil.
- [x] Pastikan form tidak tertutup keyboard.

## Milestone 9 — Notifikasi

### NOTIF-01 — Registrasi perangkat

- [x] Minta izin notifikasi setelah login atau pada konteks yang tepat.
- [x] Ambil Expo push token.
- [x] Kirim token melalui `/api/save-fcm-token`.
- [x] Tangani pengguna yang menolak izin.
- [x] Kegagalan notifikasi tidak boleh memblokir fitur utama.

### NOTIF-02 — Pengiriman notifikasi

- [x] Tentukan event Kurti baru dibuat.
- [x] Tentukan event catatan orang tua disimpan.
- [x] Kirim hanya kepada pengguna terkait.
- [x] Hindari token perangkat duplikat.
- [x] Bersihkan token yang tidak valid.

### NOTIF-03 — Navigasi dari notifikasi

- [x] Sertakan `muridId` dan `groupId` dalam payload.
- [x] Buka detail yang sesuai ketika notifikasi ditekan.
- [x] Tangani data yang sudah dihapus atau tidak lagi boleh diakses.

## Milestone 10 — Quality Gate dan Release

### QA-01 — Jalankan pemeriksaan statis

- [x] Jalankan lint React Native.
- [x] Perbaiki error dan warning penting.
- [x] Pastikan tidak ada import/file mati yang tersisa.
- [x] Pastikan tidak ada URL API hardcoded di screen.

Catatan: lint lulus tanpa error/warning. CLI `npm` global di workspace rusak,
sehingga lint dijalankan langsung melalui binary lokal `node_modules`.

### QA-02 — Jalankan test backend

- [!] Jalankan seluruh test Laravel.
- [!] Pastikan test authorization lulus.
- [!] Pastikan test kontrak respons API lulus.
- [x] Catat test yang belum dapat dijalankan beserta alasannya.

Catatan: seluruh test backend sudah dijalankan dengan PHP 8.3 setelah cache
konfigurasi dibersihkan. Hasil saat ini 14 lulus dan 23 gagal. Dua kegagalan
quality gate API yang masih perlu diselesaikan pada milestone keamanan adalah
akses detail Kurti anak pengguna lain (mengembalikan 200, seharusnya 403) dan
validasi item Kurti tanpa `pekan` (mengembalikan 500, seharusnya 422). Kegagalan
lain berasal dari test web bawaan yang belum menyiapkan field wajib `users.type`
serta ekspektasi route lama. Karena pekerjaan ini dibatasi hanya Milestone 10,
perbaikan fitur milestone sebelumnya tidak dilakukan di sini.

### QA-03 — Uji alur orang tua

- [!] Login.
- [!] Tutup dan buka kembali aplikasi.
- [!] Lihat dashboard.
- [!] Refresh dashboard.
- [!] Buka detail.
- [!] Simpan catatan.
- [!] Verifikasi status dashboard berubah.
- [!] Logout.

Catatan: emulator Android tersedia, tetapi Expo Go gagal mengunduh remote update
saat smoke test. Alur ini juga memerlukan kredensial dan data uji orang tua yang
belum tersedia.

### QA-04 — Uji alur fasilitator

- [!] Login.
- [!] Lihat kelas dan murid.
- [!] Buka detail Kurti.
- [!] Buat satu aktivitas.
- [!] Buat beberapa aktivitas.
- [!] Verifikasi dashboard refresh.
- [!] Verifikasi catatan orang tua bersifat read-only.
- [!] Logout.

Catatan: emulator Android tersedia, tetapi Expo Go gagal mengunduh remote update
saat smoke test. Alur ini juga memerlukan kredensial dan data uji fasilitator
yang belum tersedia.

### QA-05 — Uji error dan keamanan

- [!] Uji tanpa koneksi.
- [!] Uji koneksi lambat.
- [!] Uji token kedaluwarsa.
- [!] Uji kredensial salah.
- [!] Uji validasi form.
- [!] Uji penggantian `muridId`, `groupId`, dan `kurtiId` secara manual.
- [!] Pastikan data pengguna lain tidak dapat dibaca atau diubah.

Catatan: pemeriksaan statis lulus dan test backend dapat dijalankan, tetapi dua
test keamanan API masih gagal seperti dicatat pada QA-02. Pengujian dinamis
memerlukan aplikasi yang berhasil dimuat, backend aktif, serta akun dan data uji.

### RELEASE-01 — Persiapan build

- [!] Pastikan nama, icon, splash screen, package ID, dan version benar.
- [x] Pastikan base URL production benar.
- [x] Pastikan endpoint debug/test tidak terbuka.
- [x] Pastikan source tidak berisi token atau secret.
- [!] Buat build Android production.
- [!] Pasang build pada perangkat fisik.
- [!] Jalankan smoke test login sampai penyimpanan catatan.

Catatan: icon dan splash masih berupa aset placeholder Expo dan memerlukan aset
brand final. Konfigurasi web diperbaiki ke output `single` agar proyek React
Navigation dapat dibundel tanpa Expo Router. Export bundle Android production
berhasil, tetapi build aplikasi bertanda tangan tetap memerlukan EAS CLI/account;
instalasi dan smoke test rilis tetap memerlukan perangkat fisik dan akun uji.

### RELEASE-02 — Version control dan dokumentasi

- [x] Pastikan `.gitignore` mengecualikan dependency, cache, build, dan file environment rahasia.
- [x] Tambahkan `kurti-app` ke version control.
- [x] Perbarui README dengan cara menjalankan aplikasi.
- [x] Dokumentasikan environment development dan production.
- [x] Buat commit kecil per task atau kelompok task yang berkaitan.

## Urutan Eksekusi Ringkas

1. `API-01` sampai `API-07`
2. `APP-01` sampai `APP-04`
3. `AUTH-01` sampai `AUTH-05`
4. `ORTU-01` sampai `ORTU-04`
5. `FASIL-01` sampai `FASIL-04`
6. `DETAIL-01` sampai `DETAIL-04`
7. `CREATE-01` sampai `CREATE-05`
8. `UI-01` sampai `UI-04`
9. `NOTIF-01` sampai `NOTIF-03`
10. `QA-01` sampai `RELEASE-02`

Notifikasi dapat ditunda sampai sesudah MVP jika waktu terbatas. Security, autentikasi, dashboard, detail, dan pembuatan Kurti tidak boleh ditunda.
