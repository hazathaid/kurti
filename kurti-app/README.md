# Kurti Mobile

Aplikasi Expo/React Native dengan React Navigation dan satu API client terpusat.

## Menjalankan aplikasi

1. Pasang dependency dengan `npm install`.
2. Salin `.env.example` menjadi `.env.local`.
3. Atur `EXPO_PUBLIC_API_URL` ke URL server Laravel.
4. Jalankan `npm start` lalu buka aplikasi pada emulator atau perangkat.

## Development build Android

Development build digunakan ketika Expo Go tidak dapat memuat update lokal atau
fitur native perlu diuji.

1. Hubungkan emulator/perangkat dan jalankan `adb reverse tcp:8081 tcp:8081`.
2. Pasang development build dengan `npm run android`.
3. Jalankan Metro dengan `npx expo start --dev-client --localhost --clear`.
4. Buka URL development client yang ditampilkan oleh Metro.

Dependency `expo-dev-client` mengikuti versi yang direkomendasikan Expo SDK 54.

## Environment API

Expo membaca variabel `EXPO_PUBLIC_API_URL` saat bundling. Nilainya boleh berupa
origin server atau URL yang berakhir dengan `/api`; API client akan menormalkan
slash dan menambahkan `/api` jika belum ada.

- Development: gunakan alamat LAN server, misalnya
  `EXPO_PUBLIC_API_URL=http://192.168.1.10:8000/api`.
- Production: gunakan
  `EXPO_PUBLIC_API_URL=https://kurti.saisukabumi.sch.id/api`.

Jika variabel tidak diatur, aplikasi menggunakan URL production. Restart Expo
setelah mengubah environment.
