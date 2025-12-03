# 👥 User Management System - Implementation Plan

## Overview

Sistem User Management lengkap dengan Role-Based Access Control (RBAC), outlet assignment, dan authentication.

---

## 🎯 Scope

### 1. User Management

-   ✅ CRUD Users
-   ✅ User profile (name, email, phone, avatar)
-   ✅ Active/Inactive status
-   ✅ Last login tracking

### 2. Role & Permission System

-   ✅ CRUD Roles
-   ✅ Granular permissions (module → menu → action)
-   ✅ Permission actions: View, Create, Update, Delete
-   ✅ Assign permissions to roles
-   ✅ Assign role to users

### 3. Outlet Assignment

-   ✅ User can access specific outlets
-   ✅ Multi-outlet support
-   ✅ Filter data by user's outlets

### 4. Authentication

-   ✅ Login with email & password
-   ✅ Remember me
-   ✅ Logout
-   ✅ Session management

### 5. Activity Logging

-   ✅ Track user actions
-   ✅ Login/logout logs
-   ✅ CRUD operation logs
-   ✅ IP address & user agent

---

## 📊 Database Structure

### Tables

#### 1. `users`

```sql
- id
- name
- email (unique)
- password
- phone
- avatar
- role_id (FK to roles)
- is_active
- last_login_at
- remember_token
- timestamps
```

#### 2. `roles`

```sql
- id
- name (unique)
- display_name
- description
- is_active
- timestamps
```

#### 3. `permissions`

```sql
- id
- name (unique) // e.g., 'finance.jurnal.create'
- display_name
- module // e.g., 'finance'
- menu // e.g., 'jurnal'
- action // e.g., 'create'
- timestamps
```

#### 4. `role_permissions`

```sql
- id
- role_id (FK)
- permission_id (FK)
- timestamps
- unique(role_id, permission_id)
```

#### 5. `user_outlets`

```sql
- id
- user_id (FK)
- outlet_id (FK)
- timestamps
- unique(user_id, outlet_id)
```

#### 6. `user_activity_logs`

```sql
- id
- user_id (FK)
- action
- module
- description
- data (JSON)
- ip_address
- user_agent
- timestamps
```

---

## 🏗️ Architecture

### Permission Structure

```
Module (e.g., Finance)
  └─ Menu (e.g., Jurnal)
      ├─ View (finance.jurnal.view)
      ├─ Create (finance.jurnal.create)
      ├─ Update (finance.jurnal.update)
      └─ Delete (finance.jurnal.delete)
```

### Example Permissions

```
finance.jurnal.view
finance.jurnal.create
finance.jurnal.update
finance.jurnal.delete

inventaris.produk.view
inventaris.produk.create
inventaris.produk.update
inventaris.produk.delete

crm.pelanggan.view
crm.pelanggan.create
crm.pelanggan.update
crm.pelanggan.delete
```

### Predefined Roles

1. **Super Admin**

    - All permissions
    - All outlets
    - Cannot be deleted

2. **Admin**

    - Most permissions
    - Assigned outlets
    - Can manage users (except super admin)

3. **Manager**

    - View & approve permissions
    - Assigned outlets
    - Cannot delete critical data

4. **Staff**

    - Basic CRUD permissions
    - Assigned outlets
    - Limited access

5. **Viewer**
    - View-only permissions
    - Assigned outlets
    - No create/update/delete

---

## 📁 Files to Create

### Backend

#### Models (5 files)

1. `app/Models/Role.php`
2. `app/Models/Permission.php`
3. `app/Models/RolePermission.php`
4. `app/Models/UserOutlet.php`
5. `app/Models/UserActivityLog.php`
6. Update `app/Models/User.php`

#### Controllers (3 files)

1. `app/Http/Controllers/UserManagementController.php`
2. `app/Http/Controllers/RoleManagementController.php`
3. `app/Http/Controllers/AuthController.php`

#### Middleware (2 files)

1. `app/Http/Middleware/CheckPermission.php`
2. `app/Http/Middleware/CheckOutletAccess.php`

#### Requests (2 files)

1. `app/Http/Requests/UserRequest.php`
2. `app/Http/Requests/RoleRequest.php`

#### Seeders (2 files)

1. `database/seeders/RolePermissionSeeder.php`
2. `database/seeders/DefaultUserSeeder.php`

### Frontend

#### Views (6 files)

1. `resources/views/auth/login.blade.php`
2. `resources/views/admin/users/index.blade.php`
3. `resources/views/admin/users/form.blade.php`
4. `resources/views/admin/roles/index.blade.php`
5. `resources/views/admin/roles/form.blade.php`
6. `resources/views/admin/roles/permissions.blade.php`

#### Components (2 files)

1. `resources/views/components/permission-tree.blade.php`
2. `resources/views/components/outlet-selector.blade.php`

### Routes

1. Update `routes/web.php` - Add auth & user management routes

### Config

1. Update `config/auth.php` - Configure guards

---

## 🔐 Security Features

### Password

-   ✅ Bcrypt hashing
-   ✅ Minimum 8 characters
-   ✅ Must change on first login (optional)

### Session

-   ✅ Secure session handling
-   ✅ CSRF protection
-   ✅ Remember me token

### Access Control

-   ✅ Middleware for permission check
-   ✅ Middleware for outlet access
-   ✅ Route protection

### Activity Logging

-   ✅ All user actions logged
-   ✅ IP address tracking
-   ✅ User agent tracking

---

## 🎨 UI/UX Design

### Login Page

```
┌─────────────────────────────────────┐
│                                     │
│         [LOGO]                      │
│                                     │
│     Login to Your Account           │
│                                     │
│     Email: [____________]           │
│     Password: [____________]        │
│     [ ] Remember Me                 │
│                                     │
│     [Login Button]                  │
│                                     │
│     Forgot Password?                │
│                                     │
└─────────────────────────────────────┘
```

### User Management Page

```
┌─────────────────────────────────────┐
│ User Management          [+ Add]    │
├─────────────────────────────────────┤
│ [Search] [Filter Role] [Filter]     │
├─────────────────────────────────────┤
│ Name | Email | Role | Outlets | ... │
│ John | john@ | Admin | 3 | Active  │
│ Jane | jane@ | Staff | 1 | Active  │
└─────────────────────────────────────┘
```

### Role Management Page

```
┌─────────────────────────────────────┐
│ Role Management          [+ Add]    │
├─────────────────────────────────────┤
│ Role Name | Users | Permissions     │
│ Admin     | 5     | [Edit Perms]   │
│ Manager   | 10    | [Edit Perms]   │
└─────────────────────────────────────┘
```

### Permission Editor

```
┌─────────────────────────────────────┐
│ Edit Permissions: Admin Role        │
├─────────────────────────────────────┤
│ ☑ Finance                           │
│   ☑ Jurnal                          │
│     ☑ View  ☑ Create  ☑ Update  ☑ Delete
│   ☑ Buku Besar                      │
│     ☑ View  ☑ Create  ☐ Update  ☐ Delete
│                                     │
│ ☑ Inventaris                        │
│   ☑ Produk                          │
│     ☑ View  ☑ Create  ☑ Update  ☑ Delete
└─────────────────────────────────────┘
```

---

## 🚀 Implementation Steps

### Phase 1: Database & Models (Day 1)

1. ✅ Create migration
2. Run migration
3. Create models
4. Define relationships
5. Create seeders
6. Seed default data

### Phase 2: Authentication (Day 1-2)

1. Create AuthController
2. Create login view
3. Implement login logic
4. Implement logout logic
5. Create middleware
6. Test authentication

### Phase 3: User Management (Day 2-3)

1. Create UserManagementController
2. Create user views
3. Implement CRUD operations
4. Implement outlet assignment
5. Test user management

### Phase 4: Role & Permission (Day 3-4)

1. Create RoleManagementController
2. Create role views
3. Implement permission tree
4. Implement role CRUD
5. Test role management

### Phase 5: Access Control (Day 4-5)

1. Create permission middleware
2. Apply middleware to routes
3. Implement outlet filtering
4. Test access control

### Phase 6: Activity Logging (Day 5)

1. Create logging service
2. Implement log triggers
3. Create activity log view
4. Test logging

### Phase 7: Testing & Polish (Day 6)

1. Integration testing
2. UI/UX polish
3. Bug fixes
4. Documentation

---

## 📝 Usage Examples

### Check Permission in Controller

```php
if (!auth()->user()->hasPermission('finance.jurnal.create')) {
    abort(403, 'Unauthorized');
}
```

### Check Permission in Blade

```blade
@can('finance.jurnal.create')
    <button>Create Journal</button>
@endcan
```

### Check Permission in Middleware

```php
Route::get('/finance/jurnal', [JournalController::class, 'index'])
    ->middleware('permission:finance.jurnal.view');
```

### Get User Outlets

```php
$outlets = auth()->user()->outlets;
```

### Filter by User Outlets

```php
$data = Model::whereIn('outlet_id', auth()->user()->outlet_ids)->get();
```

---

## 🎯 Success Criteria

### Functional

-   ✅ Users can login/logout
-   ✅ Users can be created/updated/deleted
-   ✅ Roles can be created/updated/deleted
-   ✅ Permissions can be assigned to roles
-   ✅ Users can be assigned to outlets
-   ✅ Access control works correctly
-   ✅ Activity logs are recorded

### Security

-   ✅ Passwords are hashed
-   ✅ Sessions are secure
-   ✅ CSRF protection works
-   ✅ Unauthorized access is blocked
-   ✅ SQL injection prevented
-   ✅ XSS prevented

### UX

-   ✅ Login page is professional
-   ✅ User management is intuitive
-   ✅ Permission editor is easy to use
-   ✅ Responsive design
-   ✅ Loading states
-   ✅ Error messages

---

## 📚 Documentation

### For Admins

-   How to create users
-   How to assign roles
-   How to manage permissions
-   How to assign outlets
-   How to view activity logs

### For Developers

-   Database schema
-   API endpoints
-   Middleware usage
-   Helper functions
-   Testing guide

---

## ⚠️ Important Notes

### Super Admin

-   Cannot be deleted
-   Has all permissions
-   Has access to all outlets
-   Email: admin@system.com
-   Default password: Admin@123 (must change)

### Default Roles

-   Super Admin (cannot be deleted)
-   Admin (can be modified)
-   Manager (can be modified)
-   Staff (can be modified)
-   Viewer (can be modified)

### Permission Naming Convention

```
{module}.{menu}.{action}

Examples:
- finance.jurnal.view
- inventaris.produk.create
- crm.pelanggan.update
```

---

## 🔄 Next Steps

Karena ini adalah implementasi yang sangat besar, saya sarankan kita implementasi secara bertahap:

**Option 1: Full Implementation (6-7 days)**

-   Implement semua fitur sekaligus
-   Lengkap tapi memakan waktu

**Option 2: MVP First (2-3 days)**

-   Login/logout
-   Basic user CRUD
-   Simple role assignment
-   Basic permission check
-   Expand later

**Option 3: Modular (1-2 days per module)**

-   Day 1: Auth + Login
-   Day 2: User Management
-   Day 3: Role & Permission
-   Day 4: Access Control
-   Day 5: Polish & Test

**Recommendation**: Option 3 (Modular)

-   Bisa test setiap module
-   Bisa adjust sesuai feedback
-   Lebih manageable

---

Mau saya lanjutkan dengan implementasi? Pilih option mana atau mau saya mulai dengan MVP dulu?
