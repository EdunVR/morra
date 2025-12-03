# ✅ Checklist Deployment - Rekonsiliasi Bank

## Pre-Deployment

### 1. Code Review

-   [ ] Semua file sudah di-commit ke Git
-   [ ] Code sudah di-review oleh senior developer
-   [ ] Tidak ada hardcoded credentials
-   [ ] Environment variables sudah dikonfigurasi

### 2. Database

-   [ ] Migration file sudah ada dan valid
-   [ ] Backup database production sudah dibuat
-   [ ] Test migration di staging environment
-   [ ] Rollback plan sudah disiapkan

### 3. Dependencies

-   [ ] Composer dependencies up to date
-   [ ] NPM packages up to date (jika ada)
-   [ ] PHP version compatible (>= 8.1)
-   [ ] Laravel version compatible (>= 10.0)

### 4. Testing

-   [ ] Unit tests passed
-   [ ] Integration tests passed
-   [ ] Manual testing completed
-   [ ] Browser compatibility tested
-   [ ] Mobile responsive tested
-   [ ] Performance testing done

### 5. Documentation

-   [ ] README.md sudah lengkap
-   [ ] API documentation updated
-   [ ] User guide tersedia
-   [ ] Testing guide tersedia

---

## Deployment Steps

### Step 1: Backup

```bash
# Backup database
php artisan backup:run

# Backup files
tar -czf backup-$(date +%Y%m%d).tar.gz .
```

-   [ ] Database backup created
-   [ ] Files backup created
-   [ ] Backup verified

### Step 2: Pull Latest Code

```bash
git pull origin main
```

-   [ ] Latest code pulled
-   [ ] No merge conflicts
-   [ ] .env file updated (if needed)

### Step 3: Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

-   [ ] Dependencies installed
-   [ ] No errors during installation

### Step 4: Run Migration

```bash
php artisan migrate --force
```

-   [ ] Migration executed successfully
-   [ ] Tables created:
    -   [ ] bank_reconciliations
    -   [ ] bank_reconciliation_items
-   [ ] Foreign keys created
-   [ ] Indexes created

### Step 5: Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

-   [ ] Config cache cleared
-   [ ] Route cache cleared
-   [ ] View cache cleared
-   [ ] Application cache cleared

### Step 6: Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

-   [ ] Config cached
-   [ ] Routes cached
-   [ ] Views cached

### Step 7: Set Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

-   [ ] Storage permissions set
-   [ ] Bootstrap cache permissions set

### Step 8: (Optional) Seed Sample Data

```bash
php artisan db:seed --class=BankReconciliationSeeder
```

-   [ ] Sample data seeded (if needed)
-   [ ] Data verified

---

## Post-Deployment

### 1. Smoke Testing

-   [ ] Homepage loads correctly
-   [ ] Login works
-   [ ] Navigate to Rekonsiliasi Bank menu
-   [ ] Page loads without errors
-   [ ] Create new reconciliation works
-   [ ] Edit reconciliation works
-   [ ] Complete reconciliation works
-   [ ] Approve reconciliation works
-   [ ] Delete reconciliation works
-   [ ] Export PDF works
-   [ ] Filters work correctly

### 2. Verify Database

```sql
-- Check tables exist
SHOW TABLES LIKE 'bank_reconciliation%';

-- Check data
SELECT COUNT(*) FROM bank_reconciliations;
SELECT COUNT(*) FROM bank_reconciliation_items;

-- Check indexes
SHOW INDEX FROM bank_reconciliations;
SHOW INDEX FROM bank_reconciliation_items;
```

-   [ ] Tables exist
-   [ ] Indexes created
-   [ ] Foreign keys working

### 3. Check Logs

```bash
tail -f storage/logs/laravel.log
```

-   [ ] No errors in logs
-   [ ] No warnings in logs
-   [ ] Application running smoothly

### 4. Performance Check

-   [ ] Page load time < 2 seconds
-   [ ] Filter response time < 500ms
-   [ ] PDF generation < 3 seconds
-   [ ] No memory leaks
-   [ ] No N+1 queries

### 5. Security Check

-   [ ] CSRF protection working
-   [ ] Input validation working
-   [ ] Authorization checks working
-   [ ] SQL injection prevented
-   [ ] XSS prevented

---

## User Acceptance Testing (UAT)

### 1. Finance Team Testing

-   [ ] Finance team trained
-   [ ] Finance team tested create reconciliation
-   [ ] Finance team tested workflow
-   [ ] Finance team tested export PDF
-   [ ] Finance team feedback collected
-   [ ] Issues resolved

### 2. Manager Testing

-   [ ] Manager trained on approval process
-   [ ] Manager tested approval workflow
-   [ ] Manager tested reports
-   [ ] Manager feedback collected
-   [ ] Issues resolved

---

## Monitoring

### 1. Setup Monitoring

-   [ ] Application monitoring enabled
-   [ ] Error tracking enabled (Sentry/Bugsnag)
-   [ ] Performance monitoring enabled
-   [ ] Database monitoring enabled
-   [ ] Disk space monitoring enabled

### 2. Setup Alerts

-   [ ] Error alerts configured
-   [ ] Performance alerts configured
-   [ ] Disk space alerts configured
-   [ ] Email notifications configured

---

## Documentation

### 1. Update Documentation

-   [ ] Deployment notes documented
-   [ ] Known issues documented
-   [ ] Troubleshooting guide updated
-   [ ] FAQ updated

### 2. User Training

-   [ ] Training materials prepared
-   [ ] Training session scheduled
-   [ ] Training session conducted
-   [ ] Training feedback collected

---

## Rollback Plan

### If Something Goes Wrong:

#### Step 1: Stop Application

```bash
php artisan down
```

#### Step 2: Restore Database

```bash
# Restore from backup
mysql -u username -p database_name < backup.sql
```

#### Step 3: Restore Files

```bash
# Restore from backup
tar -xzf backup-YYYYMMDD.tar.gz
```

#### Step 4: Rollback Migration

```bash
php artisan migrate:rollback --step=1
```

#### Step 5: Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### Step 6: Bring Application Up

```bash
php artisan up
```

-   [ ] Rollback plan tested in staging
-   [ ] Rollback plan documented
-   [ ] Team knows rollback procedure

---

## Communication

### Before Deployment

-   [ ] Notify users about deployment
-   [ ] Schedule maintenance window
-   [ ] Prepare downtime message

### During Deployment

-   [ ] Put application in maintenance mode
-   [ ] Update status page
-   [ ] Monitor deployment progress

### After Deployment

-   [ ] Notify users deployment complete
-   [ ] Update status page
-   [ ] Send release notes

---

## Sign-off

### Development Team

-   [ ] Code complete and tested
-   [ ] Documentation complete
-   [ ] Ready for deployment

**Signed**: ********\_******** Date: ****\_****

### QA Team

-   [ ] Testing complete
-   [ ] No critical bugs
-   [ ] Ready for production

**Signed**: ********\_******** Date: ****\_****

### Product Owner

-   [ ] Features approved
-   [ ] UAT passed
-   [ ] Ready for release

**Signed**: ********\_******** Date: ****\_****

### Operations Team

-   [ ] Infrastructure ready
-   [ ] Monitoring configured
-   [ ] Ready to deploy

**Signed**: ********\_******** Date: ****\_****

---

## Post-Deployment Review

### 1 Day After

-   [ ] Check error logs
-   [ ] Check performance metrics
-   [ ] Check user feedback
-   [ ] Address any issues

### 1 Week After

-   [ ] Review usage statistics
-   [ ] Review performance trends
-   [ ] Collect user feedback
-   [ ] Plan improvements

### 1 Month After

-   [ ] Comprehensive review
-   [ ] Performance analysis
-   [ ] User satisfaction survey
-   [ ] Plan next iteration

---

## Success Criteria

Deployment dianggap sukses jika:

-   ✅ All smoke tests passed
-   ✅ No critical errors in logs
-   ✅ Performance meets requirements
-   ✅ Users can access and use features
-   ✅ No data loss or corruption
-   ✅ Positive user feedback

---

## Emergency Contacts

| Role           | Name       | Phone      | Email      |
| -------------- | ---------- | ---------- | ---------- |
| Lead Developer | **\_\_\_** | **\_\_\_** | **\_\_\_** |
| DevOps         | **\_\_\_** | **\_\_\_** | **\_\_\_** |
| DBA            | **\_\_\_** | **\_\_\_** | **\_\_\_** |
| Product Owner  | **\_\_\_** | **\_\_\_** | **\_\_\_** |
| Support Lead   | **\_\_\_** | **\_\_\_** | **\_\_\_** |

---

## Notes

_Add any deployment-specific notes here_

---

**Deployment Date**: ********\_********

**Deployed By**: ********\_********

**Status**: [ ] SUCCESS [ ] FAILED [ ] ROLLED BACK

**Notes**: ************************\_************************

---

**Good luck with the deployment! 🚀**
