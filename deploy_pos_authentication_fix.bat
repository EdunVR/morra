@echo off
echo ========================================
echo POS AUTHENTICATION FIX DEPLOYMENT
echo ========================================
echo.

echo Step 1: Running authentication fix script...
php fix_pos_authentication.php
echo.

echo Step 2: Clearing additional caches...
php artisan optimize:clear
echo.

echo Step 3: Regenerating optimized files...
php artisan config:cache
php artisan route:cache
echo.

echo Step 4: Checking session table...
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
try {
    \$count = DB::table('sessions')->count();
    echo \"Sessions table: {\$count} records\n\";
} catch (Exception \$e) {
    echo \"Session table error: \" . \$e->getMessage() . \"\n\";
}
"
echo.

echo Step 5: Testing POS API...
php test_pos_with_auth.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo IMPORTANT: Manual steps required:
echo 1. Clear browser cache (Ctrl+Shift+Delete)
echo 2. Clear cookies for group.dahana-boiler.com
echo 3. Close browser completely
echo 4. Login fresh to admin panel
echo 5. Test POS products loading
echo.
echo Expected result: Products should load without 401 errors
echo ========================================
pause