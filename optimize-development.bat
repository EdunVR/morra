@echo off
REM ============================================
REM Laravel Development Mode Script
REM ============================================
REM Script ini mengembalikan aplikasi ke mode development
REM Jalankan saat development untuk disable caching

echo.
echo ============================================
echo Laravel Development Mode
echo ============================================
echo.

echo Clearing all optimizations...
php artisan optimize:clear
echo Done!
echo.

echo Reinstalling Composer dependencies (with dev)...
composer install
echo Done!
echo.

echo ============================================
echo Development Mode Activated!
echo ============================================
echo.
echo All caches have been cleared.
echo You can now use env() in your code.
echo Closure routes will work.
echo Views will be recompiled on each request.
echo.

pause
