# 🚀 POS Deployment Checklist

## Pre-Deployment

### 1. Code Review

-   [ ] Semua file sudah di-commit ke Git
-   [ ] Tidak ada hardcoded credentials
-   [ ] Tidak ada console.log() di production
-   [ ] Error handling sudah lengkap
-   [ ] Validasi input sudah proper

### 2. Database

-   [ ] Migration file sudah di-review
-   [ ] Seeder sudah di-test
-   [ ] Backup database production
-   [ ] Test rollback migration

### 3. Testing

-   [ ] Unit test passed (jika ada)
-   [ ] Integration test passed
-   [ ] Manual testing completed (49 test cases)
-   [ ] Performance test passed
-   [ ] Security test passed

### 4. Documentation

-   [ ] README lengkap
-   [ ] Quick start guide tersedia
-   [ ] Testing guide tersedia
-   [ ] API documentation (jika perlu)

## Deployment Steps

### Step 1: Backup

```bash
# Backup database
mysqldump -u [user] -p [database] > backup_before_pos_$(date +%Y%m%d).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d).tar.gz app/ resources/ database/ routes/
```

-   [ ] Database backup created
-   [ ] Files backup created
-   [ ] Backup stored safely

### Step 2: Pull Code

```bash
# Pull latest code
git pull origin main

# Or upload files manually
# - app/Models/PosSale.php
# - app/Models/PosSaleItem.php
# - app/Models/SettingCOAPos.php
# - app/Http/Controllers/PosController.php
# - database/migrations/2025_11_30_create_pos_sales_tables.php
# - database/seeders/PosPermissionSeeder.php
# - resources/views/admin/penjualan/pos/*.blade.php
# - routes/web.php (updated)
```

-   [ ] Code pulled/uploaded successfully
-   [ ] File permissions correct (755 for directories, 644 for files)

### Step 3: Dependencies

```bash
# Install/update composer dependencies (if needed)
composer install --no-dev --optimize-autoloader

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

-   [ ] Dependencies installed
-   [ ] Cache cleared

### Step 4: Database Migration

```bash
# Run migration
php artisan migrate

# Check tables created
mysql -u [user] -p [database] -e "SHOW TABLES LIKE 'pos_%';"
mysql -u [user] -p [database] -e "SHOW TABLES LIKE 'setting_coa_pos';"
```

-   [ ] Migration successful
-   [ ] Tables created (pos_sales, pos_sale_items, setting_coa_pos)
-   [ ] No errors in migration

### Step 5: Seeder

```bash
# Run seeder
php artisan db:seed --class=PosPermissionSeeder

# Verify permissions
mysql -u [user] -p [database] -e "SELECT * FROM permissions WHERE module='penjualan' AND slug LIKE 'pos%';"
```

-   [ ] Seeder successful
-   [ ] 5 permissions created
-   [ ] Permissions verified

### Step 6: Permissions Setup

```bash
# Via UI or SQL
# Assign POS permissions to appropriate roles
```

-   [ ] Superadmin has all POS permissions
-   [ ] Kasir role has POS, POS Create, POS View
-   [ ] Manager role has all POS permissions including Settings
-   [ ] Other roles configured as needed

### Step 7: Setting COA

For each outlet:

-   [ ] Outlet 1: COA configured
-   [ ] Outlet 2: COA configured
-   [ ] Outlet 3: COA configured
-   [ ] All outlets: COA tested

### Step 8: Test in Production

```bash
# Test basic functionality
```

-   [ ] Can access POS menu
-   [ ] Products load correctly
-   [ ] Can create cash transaction
-   [ ] Can create transfer transaction
-   [ ] Can create bon transaction
-   [ ] Stock reduces correctly
-   [ ] Journal created correctly
-   [ ] Piutang created for bon
-   [ ] Can view history
-   [ ] Can print receipt
-   [ ] Hold order works
-   [ ] Multi outlet works

## Post-Deployment

### 1. Monitoring

-   [ ] Check error logs: `tail -f storage/logs/laravel.log`
-   [ ] Monitor database queries
-   [ ] Monitor server resources
-   [ ] Check response times

### 2. User Training

-   [ ] Train kasir on POS usage
-   [ ] Train manager on settings
-   [ ] Provide quick reference guide
-   [ ] Setup support channel

### 3. Documentation

-   [ ] Update internal wiki
-   [ ] Share quick start guide
-   [ ] Share troubleshooting guide
-   [ ] Document any custom configurations

### 4. Backup Schedule

-   [ ] Setup daily database backup
-   [ ] Setup weekly full backup
-   [ ] Test restore procedure
-   [ ] Document backup location

## Rollback Plan

If something goes wrong:

### Step 1: Stop Operations

```bash
# Put site in maintenance mode
php artisan down
```

### Step 2: Restore Database

```bash
# Restore from backup
mysql -u [user] -p [database] < backup_before_pos_YYYYMMDD.sql
```

### Step 3: Restore Files

```bash
# Restore from backup
tar -xzf backup_files_YYYYMMDD.tar.gz
```

### Step 4: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 5: Bring Site Back

```bash
php artisan up
```

### Rollback Checklist

-   [ ] Maintenance mode activated
-   [ ] Database restored
-   [ ] Files restored
-   [ ] Cache cleared
-   [ ] Site back online
-   [ ] Functionality verified
-   [ ] Users notified

## Verification

### Functional Tests

-   [ ] POS interface loads
-   [ ] Products display correctly
-   [ ] Cart functionality works
-   [ ] Transactions save correctly
-   [ ] Stock updates correctly
-   [ ] Journal entries created
-   [ ] Piutang created for bon
-   [ ] History displays correctly
-   [ ] Print works
-   [ ] Settings work

### Performance Tests

-   [ ] Page load < 2 seconds
-   [ ] Transaction save < 1 second
-   [ ] DataTable loads smoothly
-   [ ] No memory leaks
-   [ ] No slow queries

### Security Tests

-   [ ] Permissions enforced
-   [ ] CSRF protection works
-   [ ] SQL injection prevented
-   [ ] XSS prevented
-   [ ] Authentication required

## Sign-off

### Development Team

-   [ ] Developer: ********\_******** Date: **\_\_\_**
-   [ ] Code Reviewer: ******\_****** Date: **\_\_\_**
-   [ ] QA Tester: ******\_\_\_\_****** Date: **\_\_\_**

### Business Team

-   [ ] Product Owner: ******\_****** Date: **\_\_\_**
-   [ ] Business Analyst: ****\_\_**** Date: **\_\_\_**

### Operations Team

-   [ ] DevOps: ********\_\_\_******** Date: **\_\_\_**
-   [ ] System Admin: ******\_****** Date: **\_\_\_**

## Notes

### Issues Found

```
[List any issues found during deployment]
```

### Resolutions

```
[List how issues were resolved]
```

### Improvements for Next Time

```
[List any improvements for future deployments]
```

---

## 🎉 Deployment Complete!

Once all checkboxes are checked and sign-offs are complete, the POS module is officially deployed and ready for production use.

**Deployment Date**: ******\_\_\_******  
**Deployed By**: ******\_\_\_******  
**Status**: ✅ SUCCESS / ❌ FAILED / ⚠️ PARTIAL

---

**Good luck with your deployment! 🚀**
