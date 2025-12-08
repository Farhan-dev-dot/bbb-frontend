# Sistem Autentikasi Token - Panduan Penggunaan

## Yang Sudah Diimplementasi

### 1. Middleware CheckApiToken

-   **File**: `app/Http/Middleware/CheckApiToken.php`
-   **Fungsi**: Mengecek keberadaan token di session
-   **Behavior**: Redirect ke login jika tidak ada token

### 2. Middleware ValidateApiToken (Opsional)

-   **File**: `app/Http/Middleware/ValidateApiToken.php`
-   **Fungsi**: Validasi token dengan API backend
-   **Behavior**: Logout otomatis jika token expired/invalid

### 3. Route Protection

-   **File**: `routes/web.php`
-   **Implementasi**: Semua route penting dibungkus dalam middleware group
-   **Route yang diproteksi**: Dashboard, Master Data, Transaksi, Laporan

### 4. Session Manager JavaScript

-   **File**: `public/js/session-manager.js`
-   **Fungsi**: Auto-check session dan handling timeout di frontend

## Cara Menggunakan

### 1. Penggunaan Dasar (Sudah Aktif)

Middleware `check.token` sudah diterapkan ke semua route yang membutuhkan autentikasi.

### 2. Penggunaan dengan Validasi API (Opsional)

Ganti middleware di routes jika ingin validasi dengan API backend:

```php
// Di routes/web.php, ganti:
Route::middleware(['check.token'])->group(function () {
    // routes...
});

// Menjadi:
Route::middleware(['validate.token'])->group(function () {
    // routes...
});
```

### 3. Include JavaScript Session Manager

Tambahkan di layout utama (misalnya di `resources/views/layouts/app.blade.php`):

```html
<!-- Include session manager -->
<script src="{{ asset('js/session-manager.js') }}"></script>
```

### 4. Penggunaan Manual di Controller

Jika ingin mengecek token secara manual di controller:

```php
public function someMethod(Request $request)
{
    $token = $request->session()->get('access_token');

    if (!$token) {
        return redirect()->route('login')->with('error', 'Login required');
    }

    // Logic controller...
}
```

## Konfigurasi Tambahan

### 1. Timeout Session

Di `config/session.php`, atur:

```php
'lifetime' => 120, // 2 jam (dalam menit)
```

### 2. API Endpoint untuk Validasi Token

Pastikan API backend memiliki endpoint:

-   `GET /api/auth/me` - untuk validasi token
-   `POST /api/auth/refresh` - untuk refresh token (opsional)

### 3. Environment Variables

Pastikan di `.env`:

```env
APIURL=http://your-backend-api-url
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

## Flow Autentikasi

1. **Login**: Token disimpan di session
2. **Request ke halaman**: Middleware mengecek token
3. **Token valid**: Lanjut ke halaman
4. **Token tidak ada/invalid**: Redirect ke login
5. **JavaScript check**: Auto-check session di background
6. **Session expired**: Notifikasi + redirect ke login

## Keamanan

✅ **Yang sudah diimplementasi:**

-   Token disimpan di server-side session (lebih aman dari localStorage)
-   Session regeneration saat login/logout
-   Middleware protection untuk semua route penting
-   Auto-logout saat session expired
-   AJAX error handling untuk 401 responses

⚠️ **Tips tambahan:**

-   Set secure cookie di production
-   Implementasi rate limiting untuk login
-   Log semua aktivitas autentikasi
-   Implementasi CSRF protection

## Testing

Test middleware dengan cara:

1. Login normal
2. Hapus session secara manual
3. Akses halaman yang diproteksi
4. Harus redirect ke login

## Troubleshooting

**Problem**: Redirect loop ke login
**Solution**: Pastikan route login tidak menggunakan middleware check.token

**Problem**: AJAX tidak redirect
**Solution**: Handle response 401 di JavaScript untuk redirect manual

**Problem**: Session hilang tiba-tiba  
**Solution**: Cek konfigurasi session driver dan permission folder storage
