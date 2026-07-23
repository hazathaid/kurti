# Task: Pengingat Harian Kurti

Tujuan dokumen ini adalah menyiapkan pengingat harian bagi orang tua atau fasilitator jika Kurti belum diisi pada hari berjalan.

## Ruang Lingkup

- Gunakan notifikasi yang sudah ada di aplikasi sebagai dasar.
- Prioritaskan push notification dari server agar pengingat tetap jalan walau aplikasi tidak dibuka.
- Sediakan fallback local notification jika push belum siap dipakai.
- Jangan mengganggu alur login, dashboard, detail, atau pembuatan Kurti.

## Rekomendasi Teknis

- Simpan token notifikasi perangkat per user.
- Tambahkan scheduler harian di backend untuk mengecek status Kurti yang belum diisi.
- Kirim notifikasi hanya ke user yang relevan.
- Matikan pengiriman pengingat setelah Kurti hari itu sudah diisi.

## Urutan Implementasi

### PR-01 — Audit Notifikasi yang Sudah Ada

- [x] Pastikan registrasi notifikasi aktif setelah login.
- [x] Pastikan token perangkat tersimpan ke server.
- [x] Pastikan payload notifikasi bisa membawa `muridId` dan `groupId`.
- [x] Pastikan menekan notifikasi membuka detail yang benar.

### PR-02 — Definisi Kondisi Belum Diisi

- [x] Tentukan aturan “belum diisi” berdasarkan tanggal, bulan, dan pekan.
- [x] Tentukan apakah pengingat berlaku untuk orang tua, fasilitator, atau keduanya.
- [x] Tentukan jam kirim yang aman, misalnya sore atau malam hari.
- [x] Tentukan kapan pengingat berhenti dikirim.

Keputusan: tanggal dipetakan ke bulan `YYYY-MM` dan pekan
`ceil(tanggal / 7)`. Reminder pukul 18:00 `Asia/Jakarta` hanya dikirim kepada
orang tua yang terhubung. Kelompok masih pending jika sedikitnya satu aktivitas
belum memiliki catatan non-kosong dan belum ada submission untuk murid tersebut.

### PR-03 — Backend Scheduler

- [x] Tambahkan job atau command harian untuk mencari Kurti yang belum diisi.
- [x] Kirim notifikasi hanya ke user yang masih punya item pending.
- [x] Hindari pengiriman ganda pada hari yang sama.
- [x] Catat log singkat agar mudah diaudit saat gagal.

### PR-04 — Frontend Fallback

- [x] Jika push belum tersedia, jadwalkan local notification harian.
- [x] Pastikan notifikasi lokal tidak bentrok dengan push server.
- [ ] Sediakan opsi untuk mematikan reminder dari app settings jika diperlukan.

Push Expo sudah tersedia dan tervalidasi, sehingga fallback lokal tidak
diaktifkan. Ini menghindari bentrok/notifikasi ganda; kegagalan registrasi atau
pengiriman push tetap tidak memblokir fitur aplikasi.

### PR-05 — Pengujian

- [x] Uji kasus Kurti belum diisi dan notifikasi terkirim.
- [x] Uji kasus Kurti sudah diisi dan notifikasi tidak terkirim.
- [ ] Uji notifikasi ditekan dari app tertutup.
- [x] Uji pengiriman ulang pada hari berikutnya.

Navigasi dari payload `muridId`/`groupId` diaudit pada `NotificationBridge` dan
payload tujuan detail tercakup test backend. Uji tekan dari aplikasi yang benar-
benar tertutup masih memerlukan build/perangkat.

## Acceptance Criteria

- [x] Pengguna menerima pengingat harian hanya saat Kurti hari itu belum diisi.
- [x] Notifikasi tidak muncul setelah Kurti selesai diisi.
- [x] Notifikasi tetap aman jika token hilang atau user menolak izin.
- [x] Fitur utama tetap bisa dipakai walau notifikasi gagal.
