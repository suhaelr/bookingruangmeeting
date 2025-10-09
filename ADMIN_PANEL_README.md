# Admin Panel - Laravel Authentication System

Sistem autentikasi admin panel yang modern dan responsif dengan Laravel 11.

## Fitur

- ✅ Login dengan username/password admin
- ✅ Dashboard dengan tabel data responsif
- ✅ Proteksi middleware untuk akses dashboard
- ✅ Auto logout jika tidak login
- ✅ Tampilan modern dan responsif
- ✅ Kompatibel dengan berbagai device dan browser
- ✅ Search functionality
- ✅ Animasi dan transisi smooth

## Kredensial Login

- **Username:** admin
- **Password:** admin

## Instalasi

1. **Clone atau download project**
2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Build assets:**
   ```bash
   npm run build
   ```

5. **Jalankan server:**
   ```bash
   php artisan serve
   ```

6. **Akses aplikasi:**
   - URL: `http://localhost:8000`
   - Otomatis redirect ke halaman login

## Struktur File

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Handle login/logout
│   │   └── DashboardController.php # Handle dashboard
│   └── Middleware/
│       └── AdminAuth.php           # Middleware proteksi
resources/
├── views/
│   ├── auth/
│   │   └── login.blade.php         # Halaman login
│   └── dashboard.blade.php         # Halaman dashboard
├── css/
│   └── app.css                     # Custom styles
└── js/
    └── app.js                      # JavaScript functionality
routes/
└── web.php                         # Route definitions
```

## Route

- `GET /` - Redirect ke login
- `GET /login` - Halaman login
- `POST /login` - Proses login
- `POST /logout` - Proses logout
- `GET /dashboard` - Dashboard (protected)

## Middleware

- `admin.auth` - Proteksi akses dashboard
- Otomatis redirect ke login jika tidak authenticated

## Styling

- **Framework:** Tailwind CSS
- **Icons:** Font Awesome 6
- **Design:** Glass morphism effect
- **Responsive:** Mobile-first approach
- **Animations:** CSS transitions dan JavaScript effects

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Fitur Responsif

- **Desktop:** Layout penuh dengan sidebar
- **Tablet:** Layout adaptif dengan grid
- **Mobile:** Stack layout dengan navigation collapse
- **Touch:** Optimized untuk touch interaction

## Security Features

- Session-based authentication
- CSRF protection
- Input validation
- XSS protection
- Secure password handling

## Customization

### Mengubah Kredensial Login

Edit file `app/Http/Controllers/AuthController.php`:

```php
if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
    // Ganti dengan kredensial baru
}
```

### Mengubah Data Tabel

Edit file `app/Http/Controllers/DashboardController.php`:

```php
$data = [
    // Tambah/edit data di sini
];
```

### Mengubah Styling

Edit file `resources/css/app.css` untuk custom styles.

## Deployment

1. **Build untuk production:**
   ```bash
   npm run build
   composer install --no-dev --optimize-autoloader
   ```

2. **Upload ke server:**
   - Upload semua file ke `/home/ypimfbgf/public_html`
   - Pastikan web server mengarah ke folder `public`

3. **Set permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "View not found"
```bash
php artisan view:clear
php artisan config:clear
```

### Error: "Middleware not found"
```bash
php artisan config:clear
```

## Support

Untuk pertanyaan atau masalah, silakan buat issue di repository atau hubungi developer.

---

**Dibuat dengan ❤️ menggunakan Laravel 11**
