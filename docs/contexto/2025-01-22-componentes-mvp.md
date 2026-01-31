# Resumen Contexto - 22/01/2026

## Project Context
- **Backend**: Symfony 7 (PHP 8.2) with JWT authentication, MySQL database
- **Frontend**: Angular 17 with TypeScript, standalone components
- **Design System**: Boxicons + CSS variables (no Bootstrap)
- **Database**: MySQL running on 127.0.0.1:3306/seguimiento_tecnicos
- **Servers**: Backend on 127.0.0.1:8001, Frontend on localhost:4201

## What Was Done

### 1. Configuration & Authentication
- Created `.env.local` with JWT and database configuration
- Regenerated JWT keys without passphrase
- Fixed `JwtAuthenticator.php` - changed from `decode(parse())` to `parse()`
- Fixed `AssignmentController.php` QueryBuilder alias issue (`activity` → `a`)
- **CORS configured**: Installed `nelmio/cors-bundle`, configured in `.env.local` and `nelmio_cors.yaml`

### 2. Frontend Style Update (New Design)
**Reference**: `frontend/dashboard_styles_demo/` directory

Updated all components to use:
- Boxicons (`bx-*`) instead of Bootstrap icons
- CSS variables (`--light`, `--blue`, `--grey`, `--dark`, `--red`, `--yellow`, `--orange`)
- New design patterns: `.head-title`, `.box-info`, `.table-data`, `.status` badges

**Components updated**:
- ✅ `dashboard.component.scss` - Removed Bootstrap import
- ✅ `login.component.*` - Complete new design implementation
- ✅ `activities-list.component.*` - Complete new design
- ✅ `users-list.component.*` - Complete new design
- ✅ `my-assignments.component.*` - Complete new design
- ✅ `activity-detail.component.*` - Complete new design

### 3. Backend API Endpoints Created
**ActivityController.php** - Added:
- `POST /api/activities/{id}/assign` - Assign/unassign technician to activity

**UserController.php** - Already has:
- `POST /api/users` - Create user (Admin only)
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user (Admin only)
- `PUT /api/users/{id}/toggle-active` - Activate/deactivate user (Admin only)

### 4. Frontend Services Created
- ✅ `user.service.ts` - New service with `getUsers()`, `getUser()`, `getTechnicians()`, `createUser()`, `updateUser()`, `deleteUser()`, `toggleActive()`
- ✅ `assignment.service.ts` - Added `assignActivity()` and `unassignActivity()` methods

### 5. Progreso UI - Permisos en Frontend (Actualización)
- Se implementó sincronización reactiva del usuario actual en la UI para permisos:
  - ActivitiesList, UsersList y ActivityDetail se mantienen actualizados mediante suscripciones a `AuthService.currentUser$`.
- El botón Nueva Actividad verifica permisos con `canCreateActivity()` y evita navegación si no está permitido; se muestra una alerta informativa.
- Se limpian suscripciones con OnDestroy para evitar fugas de memoria.
- Este progreso refuerza la consistencia entre UI y backend en MVP Fase 1.
### 5. Components Implemented

#### ActivityDetail Component
**Files**: `activity-detail.component.ts`, `activity-detail.component.html`, `activity-detail.component.scss`

**Features**:
- Load activity by ID from URL
- Show complete information (title, description, location, dates, status, priority, creator, assigned technician)
- Start activity button (only for PENDING, technician assigned)
- Complete activity button (only for IN_PROGRESS, technician assigned)
- Cancel activity button (Admin/Coordinator only)
 - Modal to assign technician (Admin/Coordinator only for PENDING activities)
- Load available technicians list

**Permissions**:
- Admin/Coordinator: Can view, assign, cancel
- Technician: Can view only assigned activities, start, complete
- Attendee: No access

#### MyAssignments Component
**Files**: `my-assignments.component.ts`, `my-assignments.component.html`, `my-assignments.component.scss`

**Features**:
- Filter by status (All, Pending, In Progress, Completed, Cancelled)
- Show statistics (total, pending, in progress, completed)
- List technician's assigned activities
- Start/Complete activity actions directly from list
- Overdue indicator for past-due activities
- Navigate to activity detail

#### UsersList Component
**Files**: `users-list.component.ts`, `users-list.component.html`, `users-list.component.scss`

**Features**:
- Filter users by role, status (active/inactive), search by name/email
- Show statistics (total by role, active/inactive)
- Create new user modal (Admin only)
- Edit user modal (Admin: all, Coordinator: technicians only)
- Delete user (Admin only, cannot delete self)
- Toggle active status (Admin only)
- Role badges with colors

**Permission Matrix**:
| Role | Create | Edit | Delete | Toggle Active |
|------|--------|------|--------|---------------|
| Admin | All | All | All (not self) | ✓ |
| Coordinator | - | Technicians only | - | - |

### 6. Test Data Command
**File**: `backend/src/Command/LoadTestDataCommand.php`

**Executed**: `php bin/console app:load-test-data`

**Created Users**:
| Email | Password | Role | Name |
|-------|-----------|------|--------|
| admin@demo.com | admin123 | ADMIN | (via CreateAdminCommand) |
| coordinador@demo.com | coord123 | COORDINATOR | Coordinador Principal |
| tecnico1@demo.com | tecnico123 | TECHNICIAN | Juan Pérez |
| tecnico2@demo.com | tecnico123 | TECHNICIAN | María García |
| tecnico3@demo.com | tecnico123 | TECHNICIAN | Carlos López |
| tecnico4@demo.com | tecnico123 | TECHNICIAN | Ana Rodríguez |
| tecnico5@demo.com | tecnico123 | TECHNICIAN | Pedro Sánchez |

**Created Activities**: 6 activities with various priorities, some assigned to technicians

### 7. Documentation Updated
- **DOCUMENTACION.md**: Added test data command and credentials table
- **AGENTS.md**: Marked components as completed

## Current Issues (User Reported)

1. **ActivitiesList** - "No options to create activities visible"
   - Button exists in HTML at line 11-14: `createActivity()` method exists in TypeScript
   - Need to check if `canCreateActivity()` permission check is blocking it

2. **UsersList** - "No options to create users visible"
   - Button exists in HTML at line 16-20 with `@if (canCreateUser())`
   - Need to check if current user role is properly loaded

3. **UsersList** - "Edit buttons not working"
   - Permission check is `isEditAllowed(user: IUser)` - method exists
   - Need to verify if permission logic is correct

4. **ActivityDetail** - "Assign technician modal not opening"
   - Modal HTML exists at lines 212-258
   - `openAssignModal()` method exists at line 131-134
   - Button exists with `(click)="openAssignModal()"` and `@if (canAssign())` check
   - Need to check if `canAssign()` getter is returning correct value

## Files Being Modified

### Currently Examining:
1. `frontend/src/app/features/activities/pages/activities-list/activities-list.component.ts`
   - Missing `currentUser` initialization
   - Missing `canCreateActivity()` permission check
   - Methods use `getStatusBadgeClass()` / `getPriorityBadgeClass()` which return Bootstrap classes instead of new design classes

2. `frontend/src/app/features/activities/pages/activities-list/activities-list.component.html`
   - Create button exists but may be hidden by missing permission check

3. `frontend/src/app/features/users/pages/users-list/users-list.component.ts`
   - Permission checks exist: `canCreateUser()`, `isEditAllowed()`, `isDeleteAllowed()`
   - Need to verify `currentUser` is being loaded correctly from AuthService

4. `frontend/src/app/features/activities/pages/activity-detail/component.ts`
   - `canAssign()` getter exists: `return this.currentUser?.role !== 'TECHNICIAN' && (this.activity?.status === 'PENDING');`
   - Modal opening logic exists

## What Needs to Be Done Next

### Priority 1: Fix Permission Checks in ActivitiesList
1. Add `currentUser` property initialization in `ActivitiesListComponent`
2. Add `canCreateActivity()` getter method
3. Update `getStatusBadgeClass()` and `getPriorityBadgeClass()` to return new design classes (pending, process, completed, urgent, high, medium, low instead of Bootstrap classes)

### Priority 2: Verify UsersList Create Button Visibility
1. Debug why `canCreateUser()` is returning false
2. Check if `currentUser` is being loaded correctly from AuthService
3. Add console logging to debug permission checks

### Priority 3: Fix ActivityDetail Modal Not Opening
1. Check browser console for JavaScript errors
2. Verify `availableTechnicians` array is being populated
3. Check if `showAssignModal` boolean is being toggled correctly
4. Verify `@if (canAssign())` in template is evaluating correctly

## Key Technical Decisions

1. **Style System**: Abandoned Bootstrap for custom CSS with Boxicons and CSS variables
2. **Permission System**: Role-based checks on frontend (redundant with backend, but needed for UI visibility)
3. **Modal Pattern**: Overlay with `stopPropagation()` to prevent closing when clicking inside modal
4. **Angular Standalone**: All components are standalone with explicit imports
5. **TypeScript Getters**: Methods starting with `get` (like `canStart()`) must be regular methods, not getters, to avoid compilation errors

## Important Notes for Continuation

- All permission checks use `this.currentUser?.role === '...'` pattern
- Modal visibility uses `@if (showModalName)` pattern
- Backend endpoints are working correctly (tested via curl)
- Test data has been loaded successfully
- Current admin credentials: `admin@demo.com` / `admin123`
- CORS is properly configured
- Build warnings about bundle size are not critical
