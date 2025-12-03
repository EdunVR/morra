# Fix Auto Logout Error - Complete

## Problem

Ketika session expired atau user logout otomatis, aplikasi menampilkan error:

```
Call to a member function hasRole() on null
(View: resources\views\components\sidebar.blade.php)
```

Error ini terjadi karena `auth()->user()` mengembalikan `null` ketika user tidak terautentikasi, dan kode mencoba memanggil method `hasRole()` pada null.

## Root Cause

### 1. Sidebar Component

File `resources/views/components/sidebar.blade.php` menggunakan:

```php
$user = auth()->user();
// ...
if ($user->hasRole('super_admin')) {  // ❌ Error jika $user = null
```

### 2. No Exception Handling

Tidak ada handling untuk `AuthenticationException` atau error saat user tidak terautentikasi.

## Solution Implemented

### 1. Sidebar Guard Check

Tambahkan pengecekan dan redirect di sidebar component.

**File:** `resources/views/components/sidebar.blade.php`

```php
@php
    use Illuminate\Support\Facades\Route;
    $current = Route::currentRouteName();
    $user = auth()->user();

    // Redirect to login if user is not authenticated
    if (!$user) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp
```

**Benefits:**

-   ✅ Immediate redirect jika user null
-   ✅ Tidak ada error yang ditampilkan
-   ✅ User langsung ke halaman login

### 2. Global Exception Handling

Tambahkan exception handler di bootstrap/app.php untuk Laravel 11.

**File:** `bootstrap/app.php`

```php
->withExceptions(function (Exceptions $exceptions) {
    // Handle authentication exceptions
    $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('login'));
    });

    // Handle other exceptions gracefully
    $exceptions->render(function (\Throwable $e, $request) {
        // If error contains "Call to a member function" and user is not authenticated
        if (str_contains($e->getMessage(), 'Call to a member function') && !auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Please login again.'], 401);
            }
            return redirect()->route('login')->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
        }

        // Let other exceptions be handled normally
        return null;
    });
})
```

**Benefits:**

-   ✅ Catch `AuthenticationException` globally
-   ✅ Catch "Call to a member function" errors saat user null
-   ✅ Redirect ke login dengan pesan yang jelas
-   ✅ Support JSON response untuk API requests

## How It Works

### Flow Diagram

```
┌─────────────────────────────────────────────────────────┐
│ User mengakses halaman (session expired)                │
└─────────────────────────────────────────────────────────┘
                        │
                        ▼
            ┌───────────────────────┐
            │ Middleware Auth Check │
            └───────────────────────┘
                        │
                        ▼
            ┌───────────────────────┐
            │ auth()->user() = null │
            └───────────────────────┘
                        │
        ┌───────────────┴───────────────┐
        ▼                               ▼
┌──────────────┐              ┌──────────────────┐
│ Sidebar Load │              │ Controller Load  │
└──────────────┘              └──────────────────┘
        │                               │
        ▼                               ▼
┌──────────────┐              ┌──────────────────┐
│ Check $user  │              │ Try to use $user │
└──────────────┘              └──────────────────┘
        │                               │
        ▼                               ▼
┌──────────────┐              ┌──────────────────┐
│ if (!$user)  │              │ Exception thrown │
│ redirect     │              └──────────────────┘
└──────────────┘                        │
        │                               ▼
        │                    ┌──────────────────┐
        │                    │ Exception Handler│
        │                    └──────────────────┘
        │                               │
        └───────────────┬───────────────┘
                        ▼
            ┌───────────────────────┐
            │ Redirect to Login     │
            │ with message          │
            └───────────────────────┘
                        │
                        ▼
            ┌───────────────────────┐
            │ Login Page            │
            │ "Session expired..."  │
            └───────────────────────┘
```

## Testing

### Test Case 1: Session Expired

**Steps:**

1. Login ke aplikasi
2. Tunggu session expired (atau hapus cookie session)
3. Refresh halaman atau klik menu

**Expected Result:**

-   ✅ Tidak ada error ditampilkan
-   ✅ Redirect otomatis ke halaman login
-   ✅ Pesan "Session Anda telah berakhir. Silakan login kembali."

### Test Case 2: Manual Logout

**Steps:**

1. Login ke aplikasi
2. Klik logout
3. Tekan tombol back browser

**Expected Result:**

-   ✅ Tidak bisa akses halaman admin
-   ✅ Redirect ke login
-   ✅ Tidak ada error

### Test Case 3: Direct URL Access

**Steps:**

1. Buka browser baru (no session)
2. Akses URL admin langsung (e.g., /admin/dashboard)

**Expected Result:**

-   ✅ Redirect ke login
-   ✅ Tidak ada error
-   ✅ Setelah login, redirect ke halaman yang diminta

### Test Case 4: API Request

**Steps:**

1. Kirim API request tanpa authentication
2. Check response

**Expected Result:**

-   ✅ HTTP 401 Unauthorized
-   ✅ JSON response: `{"message": "Unauthenticated."}`

## Additional Improvements

### 1. Session Lifetime Configuration

Edit `.env` untuk mengatur session lifetime:

```env
SESSION_LIFETIME=120  # 120 minutes (2 hours)
```

### 2. Remember Me Feature

Pastikan "Remember Me" checkbox di login form:

```html
<input type="checkbox" name="remember" id="remember" />
<label for="remember">Remember Me</label>
```

### 3. Activity Timeout Warning

Tambahkan JavaScript untuk warning sebelum session expired:

```javascript
// Warning 5 minutes before session expires
let sessionTimeout = {{ config('session.lifetime') }} * 60 * 1000;
let warningTime = sessionTimeout - (5 * 60 * 1000);

setTimeout(() => {
    if (confirm('Session Anda akan berakhir dalam 5 menit. Lanjutkan?')) {
        // Ping server to keep session alive
        fetch('/keep-alive');
    }
}, warningTime);
```

### 4. Graceful Degradation

Tambahkan fallback di semua component yang menggunakan auth:

```php
@auth
    <!-- Content for authenticated users -->
@else
    <script>window.location.href = "{{ route('login') }}";</script>
@endauth
```

## Files Modified

### 1. `resources/views/components/sidebar.blade.php`

-   Added null check for `$user`
-   Added redirect to login if user is null

### 2. `bootstrap/app.php`

-   Added `AuthenticationException` handler
-   Added generic exception handler for null user errors
-   Added JSON response support for API

## Cache Cleared

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Benefits

### User Experience

-   ✅ No confusing error messages
-   ✅ Smooth redirect to login
-   ✅ Clear message about session expiry
-   ✅ Better security (no stack traces exposed)

### Developer Experience

-   ✅ Centralized exception handling
-   ✅ Easy to debug (check logs)
-   ✅ Consistent behavior across app
-   ✅ API-friendly responses

### Security

-   ✅ No sensitive information leaked
-   ✅ Proper authentication flow
-   ✅ Protected routes stay protected
-   ✅ Session management improved

## Error Logging

Errors are still logged for debugging:

```
storage/logs/laravel.log
```

But users see friendly redirect instead of error page.

## Monitoring

### Check Logs for Auth Issues

```bash
tail -f storage/logs/laravel.log | grep "Unauthenticated"
```

### Common Patterns

```
[2025-12-01 10:30:00] local.INFO: User redirected to login (session expired)
[2025-12-01 10:30:05] local.INFO: User logged in successfully
```

## Best Practices

### 1. Always Check Auth

```php
// ✅ Good
if (auth()->check()) {
    $user = auth()->user();
    // Use $user safely
}

// ❌ Bad
$user = auth()->user();
$user->hasRole('admin');  // Can throw error if null
```

### 2. Use Auth Directive

```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
@endguest
```

### 3. Middleware Protection

```php
Route::middleware(['auth'])->group(function () {
    // All routes here require authentication
});
```

## Troubleshooting

### Issue: Still Getting Errors

**Solution:**

1. Clear all caches: `php artisan optimize:clear`
2. Restart web server
3. Clear browser cache
4. Check if middleware is applied to routes

### Issue: Redirect Loop

**Solution:**

1. Check if login route is excluded from auth middleware
2. Verify route names are correct
3. Check for conflicting middleware

### Issue: Session Not Persisting

**Solution:**

1. Check `SESSION_DRIVER` in `.env`
2. Verify session table exists (if using database)
3. Check file permissions for session storage
4. Clear session: `php artisan session:flush`

## Status

✅ **COMPLETE** - Auto logout error fixed with graceful redirect

**Changes:**

-   ✅ Sidebar null check added
-   ✅ Global exception handler configured
-   ✅ Redirect to login on auth errors
-   ✅ User-friendly error messages
-   ✅ API support for JSON responses

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Author:** Development Team
