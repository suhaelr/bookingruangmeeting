# Instruksi Testing di Production

## Masalah yang Diperbaiki
Error 422 saat mengedit ruang dan mengubah status dari "Aktif" ke "Tidak Aktif".

## Perubahan yang Telah Dibuat
1. **Validasi `capacity`**: Diubah dari `integer` ke `numeric` untuk menangani string input
2. **Type casting**: Tambahkan `(int)$request->capacity` untuk konversi eksplisit
3. **Error logging**: Tambahkan logging detail untuk debug
4. **Error message**: Perbaiki pesan error untuk menampilkan detail validasi

## Langkah Testing di Production

### 1. Deploy Perubahan
```bash
# Di server production
git pull origin master
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart apache2
```

### 2. Test dengan Browser
1. Buka `https://pusdatinbgn.web.id/admin/rooms`
2. Buka Chrome DevTools (F12)
3. Buka tab **Console** dan **Network**
4. Klik tombol edit pada ruang yang ada
5. Ubah status dari "Aktif" ke "Tidak Aktif"
6. Klik "Perbarui Ruang"

### 3. Yang Harus Diperhatikan
- **Console tab**: Lihat log JavaScript untuk debug info
- **Network tab**: Cek request PUT ke `/admin/rooms/{id}`
- **Response**: Harus 200 OK, bukan 422

### 4. Debug Information
Jika masih error, cek:
- **Browser Console**: Akan menampilkan detail error
- **Laravel Logs**: `tail -f storage/logs/laravel.log`
- **Network Response**: Detail error dari server

### 5. Expected Behavior
- Form submit tanpa error
- Status berubah dari "Aktif" ke "Tidak Aktif"
- Halaman reload dengan data terbaru
- Tidak ada alert error

## Test Data yang Digunakan
```json
{
  "name": "awsdasd",
  "capacity": "22",
  "description": "asxcasd", 
  "location": "asdasd",
  "is_active": "0",
  "amenities": "Kursi, AC, bangku"
}
```

## Validasi Rules yang Diterapkan
```php
'name' => 'required|string|max:255',
'description' => 'nullable|string',
'capacity' => 'required|numeric|min:1',
'location' => 'required|string|max:255',
'is_active' => 'nullable|string|in:0,1,true,false,on,off,',
'amenities' => 'nullable|string'
```

## Troubleshooting

### Jika Masih Error 422:
1. Cek Laravel logs untuk detail error
2. Pastikan perubahan sudah di-deploy
3. Cek browser console untuk error JavaScript
4. Test dengan data yang berbeda

### Jika Error Berbeda:
1. Catat error message yang muncul
2. Cek network request payload
3. Cek response dari server
4. Update validasi sesuai kebutuhan

## Status
✅ Validasi rules diperbaiki
✅ Type casting ditambahkan
✅ Error logging ditingkatkan
✅ Test scripts dibuat
✅ Perubahan di-commit dan di-push

## Next Steps
1. Deploy ke production
2. Test functionality
3. Monitor logs
4. Report hasil testing
