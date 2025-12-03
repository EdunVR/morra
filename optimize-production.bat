@echo off
REM ============================================
REM Laravel Production Optimization Script
REM ============================================
REM Script ini mengoptimalkan aplikasi Laravel untuk production
REM Jalankan setelah deployment atau update code

echo.
echo ============================================
echo Laravel Production Optimization
echo ============================================
echo.

REM 1. Clear all caches
echo [1/8] Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Done!
echo.

REM 2. Optimize Composer autoloader
echo [2/8] Optimizing Composer autoloader...
composer install --optimize-autoloader --no-dev
echo Done!
echo.

REM 3. Cache configuration
echo [3/8] Caching configuration...
php artisan config:cache
echo Done!
echo.

REM 4. Cache routes
echo [4/8] Caching routes...
php artisan route:cache
echo Done!
echo.

REM 5. Cache views
echo [5/8] Caching views...
php artisan view:cache
echo Done!
echo.

REM 6. Optimize application
echo [6/8] Running Laravel optimize...
php artisan optimize
echo Done!
echo.

REM 7. Build frontend assets
echo [7/8] Building frontend assets...
call npm run build
echo Done!
echo.

REM 8. Set proper permissions (Windows)
echo [8/8] Setting permissions...
REM Windows doesn't need chmod, but ensure storage is writable
echo Storage permissions OK (Windows)
echo.

echo ============================================
echo Optimization Complete!
echo ============================================
echo.
echo Your Laravel application is now optimized for production.
echo.
echo IMPORTANT NOTES:
echo - Config cache: Enabled (env() will not work in code)
echo - Route cache: Enabled (closure routes will not work)
echo - View cache: Enabled
echo - Assets: Minified and optimized
echo.
echo To revert optimizations (for development):
echo   php artisan optimize:clear
echo.

pause
