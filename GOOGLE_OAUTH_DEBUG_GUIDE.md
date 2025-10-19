# Google OAuth Debug Guide

## Masalah: Google OAuth tidak redirect ke dashboard user

### Langkah-langkah Debug:

#### 1. **Periksa Log Laravel**
```bash
tail -f storage/logs/laravel.log
```

Cari log dengan keyword:
- `Google OAuth login successful`
- `Session verification after save`
- `UserAuth middleware check`
- `Redirecting to user dashboard`

#### 2. **Test Debug Endpoint**
Setelah login via Google, buka:
```
https://www.pusdatinbgn.web.id/debug/session
```

Periksa apakah data session ada:
- `user_logged_in`: harus `true`
- `user_data`: harus ada dengan role `user`
- `session_id`: harus ada

#### 3. **Periksa Halaman Redirect Intermediate**
Setelah login Google, Anda akan melihat halaman "Login Berhasil!" dengan loading spinner. 
- Buka Developer Tools (F12)
- Lihat Console untuk log debug
- Periksa apakah ada error JavaScript

#### 4. **Kemungkinan Penyebab Masalah:**

##### A. **Session Tidak Tersimpan**
- **Gejala**: Debug endpoint menunjukkan `user_logged_in: null`
- **Penyebab**: Masalah dengan session driver atau database
- **Solusi**: 
  ```bash
  php artisan session:table
  php artisan migrate
  ```

##### B. **Middleware UserAuth Blocking**
- **Gejala**: Log menunjukkan "User not logged in" atau "No user data found"
- **Penyebab**: Session data hilang saat redirect
- **Solusi**: Periksa session configuration


##### D. **Role Assignment Issue**
- **Gejala**: User data ada tapi role bukan 'user'
- **Penyebab**: User Google mendapat role 'admin' atau null
- **Solusi**: Periksa logic role assignment di OAuth callback

#### 5. **Test Manual:**

1. **Login via Google**
2. **Periksa halaman intermediate** - harus muncul "Login Berhasil!"
3. **Buka debug endpoint** - periksa session data
4. **Tunggu 2 detik** - harus redirect otomatis
5. **Jika tidak redirect** - klik link fallback

#### 6. **Quick Fixes:**

##### Fix 1: Force Session Regeneration
```php
// Di OAuth callback, tambahkan:
session()->regenerate();
Session::save();
```

##### Fix 2: Direct Redirect (Bypass Intermediate)
```php
// Ganti return di OAuth callback:
return redirect($redirectUrl)->with('success', 'Login berhasil!');
```

##### Fix 3: Check User Role Assignment
```php
// Pastikan user Google mendapat role 'user':
$user->update(['role' => 'user']);
```

#### 7. **Environment Variables Check:**
Pastikan file `.env` memiliki:
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
```

#### 8. **Database Session Table:**
```sql
-- Periksa apakah ada data session:
SELECT * FROM sessions WHERE id = 'YOUR_SESSION_ID';
```

### Log Messages yang Harus Muncul:

1. **OAuth Callback Success:**
```
Google OAuth login successful: {"user_id":1,"email":"user@example.com","role":"user"}
Session verification after save: {"user_logged_in":true,"user_data":{...}}
Redirecting to user dashboard
```

2. **UserAuth Middleware:**
```
UserAuth middleware check: {"user_logged_in":true,"user_data":{...}}
UserAuth: Access granted to user dashboard
```

3. **Jika Error:**
```
UserAuth: User not logged in, redirecting to login
UserAuth: No user data or role found
```

### Troubleshooting Steps:

1. **Clear all caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan session:clear
   ```

2. **Check session table:**
   ```bash
   php artisan tinker
   >>> DB::table('sessions')->get();
   ```

3. **Test with different browser/incognito mode**

4. **Check browser console for JavaScript errors**

5. **Verify Google OAuth configuration in Google Cloud Console**

### Contact Support:
Jika masalah masih berlanjut, kirimkan:
1. Log dari `storage/logs/laravel.log`
2. Output dari debug endpoint
3. Screenshot halaman intermediate
4. Browser console errors
