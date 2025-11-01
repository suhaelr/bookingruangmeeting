# UI/UX Standards - Sistem Pemesanan Ruang Meeting

## 📋 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Design System](#design-system)
3. [Color Palette](#color-palette)
4. [Typography](#typography)
5. [Spacing & Layout](#spacing--layout)
6. [Components Library](#components-library)
7. [Responsive Design](#responsive-design)
8. [Accessibility](#accessibility)
9. [User Experience Patterns](#user-experience-patterns)
10. [Indonesian Language Standards](#indonesian-language-standards)
11. [Animation & Transitions](#animation--transitions)
12. [Iconography](#iconography)

---

## 1. Pengenalan

Dokumen ini menjelaskan standar UI/UX yang harus diikuti oleh semua developer yang bekerja pada **Sistem Pemesanan Ruang Meeting**. Standar ini memastikan konsistensi, usability, dan pengalaman pengguna yang baik di seluruh aplikasi.

### Prinsip Design
1. **Konsistensi**: Semua elemen UI harus konsisten di seluruh aplikasi
2. **Kemudahan Penggunaan**: Interface harus intuitif dan mudah digunakan
3. **Responsif**: Harus bekerja dengan baik di semua device sizes
4. **Accessibility**: Dapat diakses oleh semua pengguna
5. **Visual Hierarchy**: Informasi penting harus mudah diidentifikasi
6. **Bahasa Indonesia**: Semua teks UI menggunakan bahasa Indonesia yang baik dan benar

---

## 2. Design System

### 2.1. Design Philosophy

Proyek ini menggunakan **Glass Morphism** design dengan purple gradient background. Design ini memberikan kesan modern, elegan, dan profesional.

#### Key Characteristics
- **Glass Effect**: Translucent cards dengan backdrop blur
- **Gradient Background**: Purple gradient (`linear-gradient(135deg, #667eea 0%, #764ba2 100%)`)
- **Rounded Corners**: Consistent border radius (rounded-lg, rounded-xl, rounded-2xl)
- **Shadows**: Subtle shadows untuk depth (`shadow-lg`, `shadow-2xl`)
- **Transparency**: White/colored backgrounds dengan opacity

---

## 3. Color Palette

### 3.1. Primary Colors

#### Gradient Background
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

- **Start Color**: `#667eea` (Blue-purple)
- **End Color**: `#764ba2` (Purple)
- **Usage**: Main page background (`gradient-bg` class)

#### Primary Blue
- **Color**: `#667eea` / `bg-blue-500`
- **Usage**: Primary buttons, links, active states
- **Text Color**: White

#### Secondary Purple
- **Color**: `#764ba2` / `bg-purple-600`
- **Usage**: Secondary actions, accents

### 3.2. Status Colors

#### Success (Green)
- **Color**: `#10b981` / `bg-green-500`
- **Usage**: Success messages, confirmed bookings, active states
- **Text Color**: White

```html
<div class="bg-green-500 text-white">Success Message</div>
<span class="bg-green-500 text-white rounded-full px-3 py-1">Dikonfirmasi</span>
```

#### Error (Red)
- **Color**: `#ef4444` / `bg-red-500`
- **Usage**: Error messages, cancelled bookings, danger actions
- **Text Color**: White

```html
<div class="bg-red-500 text-white">Error Message</div>
<span class="bg-red-500 text-white rounded-full px-3 py-1">Dibatalkan</span>
```

#### Warning (Yellow)
- **Color**: `#f59e0b` / `bg-yellow-500`
- **Usage**: Warning messages, pending bookings
- **Text Color**: White

```html
<div class="bg-yellow-500 text-white">Warning Message</div>
<span class="bg-yellow-500 text-white rounded-full px-3 py-1">Menunggu</span>
```

#### Info (Blue)
- **Color**: `#3b82f6` / `bg-blue-500`
- **Usage**: Information messages, info badges
- **Text Color**: White

### 3.3. Neutral Colors

#### White/Transparent
- **Glass Effect**: `rgba(255, 255, 255, 0.1)` with backdrop blur
- **Text Primary**: `text-white`
- **Text Secondary**: `text-white/80` (80% opacity)
- **Text Tertiary**: `text-white/60` (60% opacity)

#### Gray (for modals/forms)
- **Background**: White (`bg-white`)
- **Text Primary**: `text-gray-800`
- **Text Secondary**: `text-gray-600`
- **Borders**: `border-gray-300`

### 3.4. Color Usage Guidelines

#### Buttons
```html
<!-- Primary Action -->
<button class="bg-blue-500 hover:bg-blue-600 text-white">
    Pesan Ruang Meeting
</button>

<!-- Secondary Action -->
<button class="bg-white/20 hover:bg-white/30 text-white">
    Batal
</button>

<!-- Danger Action -->
<button class="bg-red-500 hover:bg-red-600 text-white">
    Hapus
</button>
```

#### Status Badges
```html
<span class="px-3 py-1 rounded-full text-sm font-medium
    @if($status === 'confirmed') bg-green-500 text-white
    @elseif($status === 'pending') bg-yellow-500 text-white
    @elseif($status === 'cancelled') bg-red-500 text-white
    @else bg-gray-500 text-white
    @endif">
    {{ $statusText }}
</span>
```

---

## 4. Typography

### 4.1. Font Family

- **Primary Font**: System fonts (`system-ui, sans-serif`)
- **Fallback**: `Inter` jika tersedia
- **Monospace**: Untuk code/number display

```css
font-family: 'Inter', system-ui, sans-serif;
```

### 4.2. Font Sizes

#### Headings
```html
<h1 class="text-4xl font-bold text-white">Main Title</h1>
<h2 class="text-2xl font-bold text-white">Section Title</h2>
<h3 class="text-xl font-bold text-white">Subsection Title</h3>
<h4 class="text-lg font-bold text-white">Card Title</h4>
```

#### Body Text
```html
<p class="text-base text-white/90">Regular body text</p>
<p class="text-sm text-white/80">Small text / Secondary info</p>
<p class="text-xs text-white/60">Extra small / Captions</p>
```

### 4.3. Font Weights

- **Bold**: `font-bold` (700) - Untuk headings, important text
- **Semibold**: `font-semibold` (600) - Untuk subheadings
- **Medium**: `font-medium` (500) - Untuk labels
- **Regular**: Default (400) - Untuk body text

### 4.4. Text Colors

#### On Gradient Background
- **Primary**: `text-white`
- **Secondary**: `text-white/80`
- **Tertiary**: `text-white/60`

#### On White/Modal Background
- **Primary**: `text-gray-800`
- **Secondary**: `text-gray-600`
- **Tertiary**: `text-gray-500`

### 4.5. Line Height

- **Headings**: Default (1.2 - 1.5)
- **Body Text**: `leading-relaxed` atau `leading-normal`
- **Compact**: `leading-tight` untuk space-constrained areas

---

## 5. Spacing & Layout

### 5.1. Container Sizes

#### Max Width
```html
<!-- Main Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>

<!-- Content Container -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>

<!-- Form Container -->
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Form -->
</div>
```

### 5.2. Padding

#### Cards/Containers
```html
<!-- Small -->
<div class="p-4">Content</div>

<!-- Medium -->
<div class="p-6">Content</div>

<!-- Large -->
<div class="p-8">Content</div>
```

#### Sections
```html
<section class="py-8">Content</section>
```

### 5.3. Gaps/Spacing

```html
<!-- Grid with gap -->
<div class="grid grid-cols-3 gap-4">
    <!-- Items -->
</div>

<!-- Flex with space -->
<div class="flex space-x-4">
    <!-- Items -->
</div>

<!-- Vertical spacing -->
<div class="space-y-4">
    <!-- Items -->
</div>
```

### 5.4. Border Radius

```html
<!-- Small -->
<div class="rounded-lg">Content</div>

<!-- Medium -->
<div class="rounded-xl">Content</div>

<!-- Large -->
<div class="rounded-2xl">Content</div>

<!-- Full -->
<div class="rounded-full">Content</div>
```

---

## 6. Components Library

### 6.1. Glass Effect Card

**Standard Glass Morphism Card**

```html
<div class="glass-effect rounded-2xl p-8 shadow-2xl">
    <!-- Content -->
</div>
```

**CSS Definition:**
```css
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
```

### 6.2. Buttons

#### Primary Button
```html
<button class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
    <i class="fas fa-calendar-plus mr-2"></i>
    Pesan Ruang Meeting
</button>
```

#### Secondary Button
```html
<button class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors duration-300">
    Batal
</button>
```

#### Danger Button
```html
<button class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300 flex items-center">
    <i class="fas fa-times mr-2"></i>
    Hapus
</button>
```

#### Icon Button
```html
<button class="p-2 text-white hover:text-blue-300 transition-colors duration-300">
    <i class="fas fa-bell text-xl"></i>
</button>
```

### 6.3. Forms

#### Input Field
```html
<div>
    <label for="field" class="block text-sm font-medium text-white mb-2">
        <i class="fas fa-envelope mr-2"></i>Label *
    </label>
    <input type="text" id="field" name="field" required
           class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
           placeholder="Masukkan nilai">
</div>
```

#### Textarea
```html
<textarea id="field" name="field" rows="3"
          class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
          placeholder="Masukkan deskripsi"></textarea>
```

#### Select Dropdown
```html
<select id="field" name="field" required
        class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
    <option value="">Pilih opsi</option>
    <option value="1">Opsi 1</option>
</select>
```

#### Form on White Background (Modal)
```html
<div class="bg-white rounded-2xl p-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Label *
    </label>
    <input type="text" name="field" required
           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>
```

### 6.4. Status Badges

```html
<!-- Dynamic Status Badge -->
<span class="px-3 py-1 rounded-full text-sm font-medium
    @if($status === 'confirmed') bg-green-500 text-white
    @elseif($status === 'pending') bg-yellow-500 text-white
    @elseif($status === 'cancelled') bg-red-500 text-white
    @elseif($status === 'completed') bg-blue-500 text-white
    @else bg-gray-500 text-white
    @endif">
    {{ $statusText }}
</span>
```

### 6.5. Alert Messages

#### Success Alert
```html
<div class="bg-green-500/20 border border-green-500/50 text-green-300 px-6 py-4 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-3"></i>
        <span>Pemesanan berhasil dibuat!</span>
    </div>
</div>
```

#### Error Alert
```html
<div class="bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-lg mb-6">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle mr-3 mt-1"></i>
        <div class="flex-1">
            <strong>Gagal membuat pemesanan</strong>
            <ul class="mt-2 list-disc list-inside">
                <li>Error message 1</li>
                <li>Error message 2</li>
            </ul>
        </div>
    </div>
</div>
```

#### Warning Alert
```html
<div class="bg-yellow-500/20 border border-yellow-500/50 text-yellow-300 px-6 py-4 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle mr-3"></i>
        <span>Peringatan: Pesan peringatan</span>
    </div>
</div>
```

### 6.6. Cards

#### Booking Card
```html
<div class="bg-white/10 rounded-lg p-6 hover:bg-white/20 transition-colors">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-white mb-1">Title</h3>
            <p class="text-white/80 text-sm">Subtitle</p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
            Status
        </span>
    </div>
    <div class="flex items-center space-x-4 text-sm text-white/60">
        <span><i class="fas fa-calendar mr-1"></i>Date</span>
        <span><i class="fas fa-clock mr-1"></i>Time</span>
    </div>
</div>
```

### 6.7. Navigation

#### Desktop Navigation
```html
<nav class="glass-effect shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <!-- Logo/Menu -->
            </div>
            <div class="flex items-center space-x-2">
                <!-- Actions -->
            </div>
        </div>
    </div>
</nav>
```

#### Mobile Sidebar
```html
<!-- Include component -->
@include('components.mobile-sidebar', [
    'userRole' => 'user',
    'userName' => session('user_data.full_name'),
    'userEmail' => session('user_data.email'),
    'pageTitle' => 'Dashboard'
])
```

### 6.8. Modals

```html
<!-- Modal Overlay -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Modal Title</h3>
                <button onclick="closeModal('modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div id="modalContent">
                <!-- Content -->
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 mt-6">
                <button onclick="closeModal('modal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 7. Responsive Design

### 7.1. Breakpoints

```css
/* Tailwind Default Breakpoints */
sm:  640px   /* Small devices (landscape phones) */
md:  768px   /* Medium devices (tablets) */
lg:  1024px  /* Large devices (desktops) */
xl:  1280px  /* Extra large devices */
2xl: 1536px  /* 2X Extra large devices */
```

### 7.2. Mobile-First Approach

```html
<!-- Base styles for mobile -->
<div class="grid grid-cols-1 gap-4">
    <!-- Mobile: 1 column -->
</div>

<!-- Tablet and up -->
<div class="md:grid-cols-2 gap-4">
    <!-- Tablet: 2 columns -->
</div>

<!-- Desktop and up -->
<div class="lg:grid-cols-3 gap-4">
    <!-- Desktop: 3 columns -->
</div>
```

### 7.3. Responsive Text

```html
<!-- Responsive heading -->
<h1 class="text-2xl md:text-3xl lg:text-4xl font-bold">Title</h1>

<!-- Responsive padding -->
<div class="p-4 md:p-6 lg:p-8">Content</div>
```

### 7.4. Responsive Forms

```html
<!-- Single column on mobile, two columns on desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label>Field 1</label>
        <input type="text">
    </div>
    <div>
        <label>Field 2</label>
        <input type="text">
    </div>
</div>
```

---

## 8. Accessibility

### 8.1. Semantic HTML

```html
<!-- ✅ Good - Semantic HTML -->
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
        </ul>
    </nav>
</header>

<main>
    <section>
        <h1>Page Title</h1>
        <article>
            <!-- Content -->
        </article>
    </section>
</main>

<!-- ❌ Bad - Div soup -->
<div class="header">
    <div class="nav">
        <div class="menu">
            <div><div>Home</div></div>
        </div>
    </div>
</div>
```

### 8.2. ARIA Labels

```html
<!-- Button dengan icon -->
<button aria-label="Tutup modal" onclick="closeModal()">
    <i class="fas fa-times"></i>
</button>

<!-- Form dengan error -->
<div role="alert" class="bg-red-500/20">
    <span>Error message</span>
</div>
```

### 8.3. Keyboard Navigation

```javascript
// Support keyboard navigation
document.addEventListener('keydown', function(e) {
    // ESC key untuk tutup modal
    if (e.key === 'Escape') {
        closeModal();
    }
    
    // Tab navigation
    if (e.key === 'Tab') {
        // Focus management
    }
});
```

### 8.4. Color Contrast

- **Minimum Contrast Ratio**: 4.5:1 untuk normal text
- **Large Text**: 3:1 untuk text 18pt+ atau 14pt+ bold
- **Interactive Elements**: 3:1 untuk UI components

### 8.5. Focus States

```html
<!-- ✅ Good - Visible focus states -->
<button class="focus:outline-none focus:ring-2 focus:ring-white/50">
    Button
</button>

<!-- ❌ Bad - No focus state -->
<button class="focus:outline-none">
    Button
</button>
```

---

## 9. User Experience Patterns

### 9.1. Loading States

```html
<!-- Loading Button -->
<button disabled class="bg-gray-400 cursor-not-allowed">
    <i class="fas fa-spinner fa-spin mr-2"></i>
    Memproses...
</button>

<!-- Loading Spinner -->
<div class="flex items-center justify-center p-8">
    <i class="fas fa-spinner fa-spin text-4xl text-white"></i>
</div>
```

### 9.2. Empty States

```html
<div class="text-center py-12">
    <i class="fas fa-calendar-times text-white/40 text-6xl mb-4"></i>
    <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Pemesanan</h3>
    <p class="text-white/60 mb-6">Anda belum membuat pemesanan ruang meeting.</p>
    <a href="{{ route('user.bookings.create') }}" 
       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Buat Pemesanan Pertama
    </a>
</div>
```

### 9.3. Success Feedback

```html
<!-- Success Toast -->
<div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
</div>

<!-- Auto-hide JavaScript -->
<script>
setTimeout(() => {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        successMessage.style.transition = 'opacity 0.5s';
        successMessage.style.opacity = '0';
        setTimeout(() => successMessage.remove(), 500);
    }
}, 1000); // Hide after 1 second
</script>
```

### 9.4. Form Validation

```html
<!-- Real-time validation -->
<div>
    <label>Email *</label>
    <input type="email" id="email" name="email" required
           class="border-red-500 focus:ring-red-500">
    <p class="text-red-300 text-sm mt-1">Format email tidak valid</p>
</div>
```

### 9.5. Confirmation Dialogs

```html
<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Hapus Booking</h3>
                    <p class="text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus booking ini?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeModal('confirmModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Batal
                </button>
                <button onclick="confirmDelete()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 10. Indonesian Language Standards

### 10.1. General Rules

1. **Gunakan bahasa Indonesia yang baik dan benar**
2. **Hindari campuran bahasa Inggris kecuali istilah teknis**
3. **Gunakan istilah yang konsisten di seluruh aplikasi**
4. **Format teks menggunakan EYD yang benar**

### 10.2. Common Terms

#### Status
- `pending` → "Menunggu"
- `confirmed` → "Dikonfirmasi"
- `cancelled` → "Dibatalkan"
- `completed` → "Selesai"

#### Actions
- `Create` → "Buat" atau "Tambah"
- `Update` → "Perbarui" atau "Ubah"
- `Delete` → "Hapus"
- `Cancel` → "Batal"
- `Save` → "Simpan"
- `Submit` → "Kirim"
- `Search` → "Cari"
- `Filter` → "Filter"

#### Form Labels
- `Full Name` → "Nama Lengkap"
- `Email Address` → "Alamat Email"
- `Phone Number` → "Nomor Telepon"
- `Unit Kerja` → "Unit Kerja"
- `Meeting Room` → "Ruang Meeting"
- `Start Time` → "Mulai Waktu"
- `End Time` → "Selesai Waktu"

### 10.3. Error Messages

```php
// ✅ Good - Indonesian error messages
'email.required' => 'Alamat email wajib diisi.',
'email.email' => 'Format email tidak valid.',
'email.unique' => 'Alamat email sudah terdaftar.',

// ❌ Bad - English error messages
'email.required' => 'Email is required.',
```

### 10.4. Success Messages

```php
// ✅ Good - Indonesian success messages
session('success', 'Pemesanan berhasil dibuat!');
session('success', 'Profil berhasil diperbarui!');

// ❌ Bad - English success messages
session('success', 'Booking created successfully!');
```

---

## 11. Animation & Transitions

### 11.1. Transitions

#### Button Hover
```html
<button class="bg-blue-500 hover:bg-blue-600 transition-colors duration-300">
    Button
</button>
```

#### Card Hover
```html
<div class="bg-white/10 hover:bg-white/20 transition-colors duration-300">
    Card Content
</div>
```

#### Transform Hover
```html
<button class="transform hover:scale-105 transition-transform duration-300">
    Button
</button>
```

### 11.2. Fade In Animation

```css
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}
```

```html
<div class="fade-in">Content</div>
```

### 11.3. Loading Spinner

```html
<i class="fas fa-spinner fa-spin"></i>
```

---

## 12. Iconography

### 12.1. Font Awesome Icons

Proyek ini menggunakan **Font Awesome 6.0.0**.

#### Common Icons

```html
<!-- Navigation -->
<i class="fas fa-calendar-alt"></i>         <!-- Calendar / System -->
<i class="fas fa-bars"></i>                  <!-- Menu -->
<i class="fas fa-times"></i>                 <!-- Close -->

<!-- Actions -->
<i class="fas fa-plus"></i>                  <!-- Add / Create -->
<i class="fas fa-edit"></i>                  <!-- Edit -->
<i class="fas fa-trash"></i>                 <!-- Delete -->
<i class="fas fa-save"></i>                  <!-- Save -->
<i class="fas fa-sign-out-alt"></i>          <!-- Logout -->
<i class="fas fa-eye"></i>                   <!-- View -->

<!-- Status -->
<i class="fas fa-check-circle"></i>         <!-- Success -->
<i class="fas fa-exclamation-triangle"></i>  <!-- Warning -->
<i class="fas fa-times-circle"></i>          <!-- Error -->
<i class="fas fa-info-circle"></i>           <!-- Info -->

<!-- Booking -->
<i class="fas fa-door-open"></i>            <!-- Meeting Room -->
<i class="fas fa-clock"></i>                 <!-- Time -->
<i class="fas fa-calendar"></i>              <!-- Date -->
<i class="fas fa-users"></i>                 <!-- Attendees -->

<!-- Profile -->
<i class="fas fa-user"></i>                  <!-- User -->
<i class="fas fa-envelope"></i>              <!-- Email -->
<i class="fas fa-phone"></i>                 <!-- Phone -->
<i class="fas fa-building"></i>              <!-- Building / Unit Kerja -->

<!-- Notification -->
<i class="fas fa-bell"></i>                  <!-- Notifications -->

<!-- Others -->
<i class="fas fa-search"></i>                <!-- Search -->
<i class="fas fa-filter"></i>                <!-- Filter -->
<i class="fas fa-download"></i>              <!-- Download / Export -->
```

### 12.2. Icon Usage Guidelines

1. **Size**: Gunakan `text-xl`, `text-2xl`, `text-3xl` untuk konsistensi
2. **Spacing**: Selalu beri margin dengan `mr-2` atau `ml-2`
3. **Color**: Ikuti warna parent element atau gunakan `text-white` untuk dark backgrounds
4. **Alignment**: Gunakan `flex items-center` untuk vertical alignment

```html
<!-- ✅ Good - Proper icon usage -->
<button class="flex items-center">
    <i class="fas fa-plus mr-2"></i>
    <span>Buat Baru</span>
</button>

<!-- ❌ Bad - No spacing -->
<button>
    <i class="fas fa-plus"></i>Buat Baru
</button>
```

---

## 13. Best Practices Summary

### UI Best Practices
1. ✅ **Gunakan glass-effect** untuk cards pada gradient background
2. ✅ **Gunakan white background** untuk modals dan forms
3. ✅ **Konsisten dengan spacing** menggunakan Tailwind scale
4. ✅ **Responsive design** dengan mobile-first approach
5. ✅ **Gunakan status colors** dengan konsisten
6. ✅ **Transitions** untuk semua interactive elements
7. ✅ **Loading states** untuk semua async operations
8. ✅ **Empty states** dengan clear call-to-action
9. ✅ **Error messages** yang jelas dan actionable
10. ✅ **Success feedback** yang timely dan non-intrusive

### UX Best Practices
1. ✅ **Bahasa Indonesia** di seluruh aplikasi
2. ✅ **Clear navigation** dengan breadcrumbs jika perlu
3. ✅ **Form validation** dengan real-time feedback
4. ✅ **Confirmation dialogs** untuk destructive actions
5. ✅ **Keyboard navigation** support
6. ✅ **Accessibility** dengan ARIA labels
7. ✅ **Loading states** untuk better perceived performance
8. ✅ **Error handling** dengan helpful messages
9. ✅ **Consistent patterns** di seluruh aplikasi
10. ✅ **User feedback** untuk semua actions

---

## 14. Component Checklist

Sebelum menggunakan/membuat component, pastikan:

- [ ] Mengikuti glass-effect pattern atau white background sesuai context
- [ ] Menggunakan color palette yang sudah ditentukan
- [ ] Typography mengikuti hierarchy yang benar
- [ ] Spacing konsisten dengan Tailwind scale
- [ ] Responsive di semua breakpoints
- [ ] Accessible (ARIA labels, keyboard navigation)
- [ ] Loading/empty/error states tersedia
- [ ] Transitions untuk interactive elements
- [ ] Bahasa Indonesia yang baik dan benar
- [ ] Icons dengan spacing dan alignment yang benar

---

**Last Updated**: 2025-01-XX
**Version**: 1.0.0

