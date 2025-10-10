# Google Cloud Console Setup untuk OAuth

## Masalah: redirect_uri_mismatch

Error ini terjadi karena callback URL di Google Cloud Console tidak sesuai dengan yang digunakan di aplikasi.

## Langkah-langkah Perbaikan:

### 1. **Buka Google Cloud Console**
- Kunjungi: https://console.cloud.google.com/
- Pilih project Anda

### 2. **Pergi ke APIs & Services > Credentials**
- Klik pada OAuth 2.0 Client ID yang sudah ada
- Atau buat yang baru jika belum ada

### 3. **Update Authorized redirect URIs**
Tambahkan URL berikut ke **Authorized redirect URIs**:

```
https://www.pusdatinbgn.web.id/auth/google/callback
http://localhost:8000/auth/google/callback
```

### 4. **Update Authorized JavaScript origins**
Tambahkan URL berikut ke **Authorized JavaScript origins**:

```
https://www.pusdatinbgn.web.id
http://localhost:8000
```

### 5. **Update OAuth Consent Screen**
Pastikan pengaturan berikut:
- **App name**: `Sistem Pemesanan Ruang Meeting`
- **User support email**: `suhaelr@gmail.com`
- **Developer contact**: `suhaelr@gmail.com`

### 6. **Scopes yang Diperlukan**
Pastikan scopes berikut ditambahkan:
- `../auth/userinfo.email`
- `../auth/userinfo.profile`
- `openid`

### 7. **Test OAuth Flow**
1. Simpan perubahan di Google Cloud Console
2. Tunggu 5-10 menit untuk propagasi
3. Test login via Google di aplikasi

## Troubleshooting:

### Jika masih error redirect_uri_mismatch:
1. **Periksa URL yang tepat** - pastikan tidak ada typo
2. **Pastikan HTTPS** - Google memerlukan HTTPS untuk production
3. **Tunggu propagasi** - perubahan bisa memakan waktu 5-10 menit
4. **Clear browser cache** - hapus cache browser

### Jika error 401 Unauthorized:
1. **Periksa Client ID dan Secret** - pastikan benar
2. **Periksa environment variables** - pastikan `.env` sudah benar
3. **Periksa OAuth Consent Screen** - pastikan sudah dikonfigurasi

### Debug OAuth Flow:
1. **Test endpoint**: `https://www.pusdatinbgn.web.id/test/oauth`
2. **Debug session**: `https://www.pusdatinbgn.web.id/debug/session`
3. **Periksa log Laravel**: `storage/logs/laravel.log`

## Environment Variables yang Diperlukan:

```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=https://www.pusdatinbgn.web.id/auth/google/callback
```

## Verifikasi Konfigurasi:

Setelah mengupdate Google Cloud Console, test dengan:
1. Login via Google
2. Periksa apakah redirect ke dashboard berhasil
3. Periksa log untuk memastikan tidak ada error

Jika masih ada masalah, periksa:
- URL callback di Google Cloud Console
- Environment variables di `.env`
- Log Laravel untuk error detail
