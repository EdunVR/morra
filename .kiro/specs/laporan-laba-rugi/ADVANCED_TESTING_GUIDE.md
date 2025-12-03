# Advanced Testing Guide - Laporan Laba Rugi

## Overview

This document provides comprehensive guidance for advanced testing of the Laporan Laba Rugi (Profit & Loss Report) feature, including performance testing, load testing, and security testing.

## Test Files

### 1. Performance Tests

**File**: `tests/Feature/ProfitLossPerformanceTest.php`

Tests system performance under various data loads and scenarios.

#### Test Cases

##### test_profit_loss_performance_with_large_account_dataset

-   **Purpose**: Test performance with 1000+ accounts and hierarchical structure
-   **Setup**:
    -   50 parent revenue accounts with 10 children each (500 total)
    -   50 parent expense accounts with 10 children each (500 total)
    -   1000 journal entries with 5 details each
-   **Assertion**: Query should complete within 10 seconds
-   **Metrics**: Setup time, query time, total accounts, journals, and details

##### test_profit_loss_performance_with_comparison_mode

-   **Purpose**: Test performance when comparison mode is enabled
-   **Setup**:
    -   500 journals for current period
    -   500 journals for comparison period
-   **Assertion**: Comparison query should complete within 15 seconds
-   **Metrics**: Query time, total journals

##### test_profit_loss_memory_usage

-   **Purpose**: Monitor memory consumption during report generation
-   **Setup**:
    -   1 parent account with 100 children
    -   500 journals with 10 details each
-   **Assertion**: Memory usage should be less than 128MB
-   **Metrics**: Memory used, peak memory

##### test_profit_loss_query_optimization

-   **Purpose**: Verify query optimization and eager loading
-   **Setup**: 100 journals with details
-   **Assertion**: Should use less than 50 queries
-   **Metrics**: Total queries, top 5 slowest queries

##### test_profit_loss_export_performance

-   **Purpose**: Test export functionality performance
-   **Setup**: 50 accounts, 200 journals
-   **Assertion**: Both XLSX and PDF exports should complete within 30 seconds
-   **Metrics**: XLSX export time, PDF export time

### 2. Load Tests

**File**: `tests/Feature/ProfitLossLoadTest.php`

Tests system behavior under concurrent load and stress conditions.

#### Test Cases

##### test_concurrent_profit_loss_requests

-   **Purpose**: Test handling of concurrent requests
-   **Setup**: 10 simultaneous requests to the same endpoint
-   **Assertions**:
    -   All requests should succeed (200 status)
    -   Average response time < 5 seconds
-   **Metrics**: Total time, average/min/max response time, requests per second

##### test_multi_user_multi_outlet_load

-   **Purpose**: Test multiple users accessing different outlets simultaneously
-   **Setup**:
    -   5 users
    -   5 outlets with 50 journals each
-   **Assertions**: All requests should succeed
-   **Metrics**: Response time per user/outlet combination

##### test_rapid_filter_changes

-   **Purpose**: Test system stability with rapid filter changes
-   **Setup**: Test 5 different date ranges (7, 30, 90, 180, 365 days)
-   **Assertions**: All requests should succeed
-   **Metrics**: Response time per period

##### test_cache_effectiveness_under_load

-   **Purpose**: Measure cache performance improvement
-   **Setup**: First request (cache miss) followed by 5 cached requests
-   **Metrics**:
    -   First request time (cache miss)
    -   Average cached request time
    -   Speed improvement ratio

##### test_export_under_load

-   **Purpose**: Test export functionality under load
-   **Setup**: Test both XLSX and PDF exports
-   **Assertions**: All exports should succeed
-   **Metrics**: Export time per format

### 3. Security Tests

**File**: `tests/Feature/ProfitLossSecurityTest.php`

Tests security vulnerabilities and access controls.

#### Test Cases

##### test_profit_loss_requires_authentication

-   **Purpose**: Verify authentication requirement
-   **Assertion**: Unauthenticated requests should return 401

##### test_sql_injection_prevention_outlet_id

-   **Purpose**: Test SQL injection prevention in outlet_id parameter
-   **Malicious Inputs**:
    -   `1' OR '1'='1`
    -   `1; DROP TABLE journal_entries--`
    -   `1 UNION SELECT * FROM users--`
    -   `1' AND 1=1--`
    -   `1' OR 'a'='a`
-   **Assertion**: Should return 400, 404, or 422 (not 500)

##### test_sql_injection_prevention_dates

-   **Purpose**: Test SQL injection prevention in date parameters
-   **Malicious Inputs**:
    -   `2024-01-01' OR '1'='1`
    -   `2024-01-01; DROP TABLE--`
    -   `2024-01-01' UNION SELECT--`
-   **Assertion**: Should return 400 or 422 (not 500)

##### test_xss_prevention_in_response

-   **Purpose**: Verify XSS attack prevention
-   **Setup**: Create account with `<script>alert("XSS")</script>` in name
-   **Assertion**: Response should be valid JSON without executable scripts

##### test_unauthorized_outlet_access

-   **Purpose**: Verify outlet access control
-   **Setup**: Attempt to access another user's outlet
-   **Assertion**: Should return 403 or 404

##### test_parameter_tampering_prevention

-   **Purpose**: Test parameter validation
-   **Tampering Attempts**:
    -   Negative outlet_id
    -   Non-existent outlet_id
    -   Invalid data types
    -   Null/empty values
-   **Assertion**: Should return validation errors

##### test_date_range_validation

-   **Purpose**: Verify date range validation
-   **Test Cases**:
    -   End date before start date
    -   Invalid date format
    -   Future dates
-   **Assertion**: Should return 422 for invalid ranges

##### test_mass_assignment_protection

-   **Purpose**: Verify protection against mass assignment
-   **Setup**: Inject additional parameters (is_admin, user_id, role)
-   **Assertion**: Should ignore extra parameters and process normally

##### test_csrf_protection_on_exports

-   **Purpose**: Verify CSRF protection
-   **Assertion**: GET requests should not require CSRF token

##### test_rate_limiting

-   **Purpose**: Document rate limiting behavior
-   **Setup**: Make 100 rapid requests
-   **Metrics**: Total requests made, whether rate limited

##### test_no_sensitive_data_in_errors

-   **Purpose**: Verify error messages don't expose sensitive data
-   **Assertion**: Error responses should not contain:
    -   SQL queries
    -   Database structure
    -   File paths
    -   Stack traces

##### test_comparison_mode_validation

-   **Purpose**: Verify comparison mode parameter validation
-   **Test Cases**:
    -   Comparison enabled without dates
    -   Invalid comparison date range
-   **Assertion**: Should handle gracefully

##### test_export_filename_sanitization

-   **Purpose**: Verify export filename security
-   **Assertion**: Filenames should not contain:
    -   Path traversal characters (..)
    -   Slashes (/ or \)

##### test_input_length_limits

-   **Purpose**: Test handling of extremely long inputs
-   **Setup**: 1000-character outlet_id
-   **Assertion**: Should handle gracefully with validation error

##### test_stats_endpoint_security

-   **Purpose**: Verify stats endpoint security
-   **Assertions**:
    -   Unauthenticated access returns 401
    -   Authenticated access works or returns validation error

## Running the Tests

### Run All Advanced Tests

```bash
php artisan test --testsuite=Feature --filter=ProfitLoss
```

### Run Performance Tests Only

```bash
php artisan test tests/Feature/ProfitLossPerformanceTest.php
```

### Run Load Tests Only

```bash
php artisan test tests/Feature/ProfitLossLoadTest.php
```

### Run Security Tests Only

```bash
php artisan test tests/Feature/ProfitLossSecurityTest.php
```

### Run with Verbose Output

```bash
php artisan test tests/Feature/ProfitLossPerformanceTest.php --testdox
```

## Performance Benchmarks

### Expected Performance Metrics

| Scenario                   | Expected Time | Max Acceptable |
| -------------------------- | ------------- | -------------- |
| Query with 1000 accounts   | < 5s          | 10s            |
| Query with comparison mode | < 10s         | 15s            |
| XLSX Export                | < 20s         | 30s            |
| PDF Export                 | < 20s         | 30s            |
| Concurrent requests (avg)  | < 3s          | 5s             |
| Memory usage               | < 64MB        | 128MB          |

### Query Optimization Targets

-   Total queries per request: < 50
-   Use of eager loading: Yes
-   Database indexes utilized: Yes
-   N+1 query problems: None

## Security Checklist

### Authentication & Authorization

-   [x] Requires authentication
-   [x] Validates outlet access
-   [x] Prevents unauthorized data access

### Input Validation

-   [x] SQL injection prevention
-   [x] XSS prevention
-   [x] Parameter tampering prevention
-   [x] Date range validation
-   [x] Input length limits

### Data Protection

-   [x] No sensitive data in errors
-   [x] Secure file name generation
-   [x] Mass assignment protection

### API Security

-   [x] CSRF protection (where applicable)
-   [x] Rate limiting (optional)
-   [x] Proper error codes

## Load Testing Scenarios

### Scenario 1: Normal Load

-   **Users**: 10 concurrent
-   **Duration**: 5 minutes
-   **Expected**: All requests succeed, avg response < 3s

### Scenario 2: Peak Load

-   **Users**: 50 concurrent
-   **Duration**: 10 minutes
-   **Expected**: > 95% success rate, avg response < 5s

### Scenario 3: Stress Test

-   **Users**: 100 concurrent
-   **Duration**: 5 minutes
-   **Expected**: System remains stable, graceful degradation

### Scenario 4: Endurance Test

-   **Users**: 20 concurrent
-   **Duration**: 1 hour
-   **Expected**: No memory leaks, consistent performance

## Troubleshooting

### Performance Issues

**Symptom**: Queries taking longer than expected

**Solutions**:

1. Check database indexes
2. Review query execution plans
3. Implement query result caching
4. Optimize eager loading
5. Consider database query optimization

**Symptom**: High memory usage

**Solutions**:

1. Implement pagination for large datasets
2. Use chunking for batch operations
3. Clear unnecessary data from memory
4. Optimize data structures

### Load Issues

**Symptom**: Concurrent requests failing

**Solutions**:

1. Check database connection pool
2. Review server resources (CPU, RAM)
3. Implement request queuing
4. Add load balancing

**Symptom**: Export timeouts

**Solutions**:

1. Move exports to background jobs
2. Implement streaming for large files
3. Optimize export queries
4. Add progress indicators

### Security Issues

**Symptom**: Validation bypassed

**Solutions**:

1. Review validation rules
2. Implement server-side validation
3. Add input sanitization
4. Use Laravel's validation features

**Symptom**: Unauthorized access

**Solutions**:

1. Implement proper authorization policies
2. Add middleware for access control
3. Verify outlet ownership
4. Log access attempts

## Best Practices

### Performance Testing

1. Test with realistic data volumes
2. Measure baseline performance
3. Test incrementally (small → large datasets)
4. Monitor system resources
5. Document performance metrics

### Load Testing

1. Start with low load and increase gradually
2. Test different user scenarios
3. Monitor error rates
4. Check for memory leaks
5. Test during peak hours

### Security Testing

1. Test all input parameters
2. Verify authentication on all endpoints
3. Test authorization boundaries
4. Check error message content
5. Validate file operations
6. Test with malicious inputs

## Continuous Monitoring

### Metrics to Track

-   Average response time
-   95th percentile response time
-   Error rate
-   Memory usage
-   Database query count
-   Cache hit rate

### Alerting Thresholds

-   Response time > 10s
-   Error rate > 5%
-   Memory usage > 80%
-   Database connection pool > 80%

## Conclusion

Advanced testing ensures the Laporan Laba Rugi feature is:

-   **Performant**: Handles large datasets efficiently
-   **Scalable**: Supports concurrent users
-   **Secure**: Protects against common vulnerabilities
-   **Reliable**: Maintains stability under load

Regular execution of these tests helps maintain system quality and identify issues before they impact users.
