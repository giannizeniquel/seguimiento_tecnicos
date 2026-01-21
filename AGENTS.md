# AGENTS.md - Instrucciones para Agentes de IA

Este archivo contiene las instrucciones y comandos que los agentes de IA deben conocer para trabajar en este proyecto.

## 📋 Información del Proyecto

**Nombre**: Sistema de Seguimiento de Técnicos en Campo  
**Stack**: Symfony 7 + Angular 17 + PWA  
**Base de Datos**: SQLite (desarrollo) / MySQL (producción)  
**Estilos**: Bootstrap 5.3

---

## 🏗️ Estructura del Proyecto

```
seguimiento_tecnicos/
├── backend/          # Backend Symfony 7
├── frontend/         # Frontend Angular 17
└── docs/             # Documentación
```

---

## 🔧 Comandos del Backend (Symfony)

### Desarrollo

```bash
# Ir al directorio del backend
cd backend

# Instalar dependencias
composer install

# Ejecutar migraciones de base de datos
php bin/console doctrine:migration:migrate

# Crear nueva migración
php bin/console doctrine:migration:diff

# Limpiar caché
php bin/console cache:clear

# Iniciar servidor de desarrollo
php -S localhost:8000 -t public
```

### Tests y Lint

```bash
# Ejecutar tests PHPUnit
php bin/phpunit

# Ejecutar linter de código
vendor/bin/phpstan analyse src

# Ejecutar PHP CS Fixer
vendor/bin/php-cs-fixer fix
```

### Generación de Código

```bash
# Crear entidad
php bin/console make:entity

# Crear controlador
php bin/console make:controller

# Crear formulario
php bin/console make:form

# Crear servicio
php bin/console make:service

# Crear evento
php bin/console make:subscriber

# Crear validator
php bin/console make:validator
```

### JWT Authentication

```bash
# Regenerar claves JWT
openssl genrsa -passout pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -aes256 4096 -out config/jwt/private.pem
openssl rsa -passin pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

---

## 🔧 Comandos del Frontend (Angular)

### Desarrollo

```bash
# Ir al directorio del frontend
cd frontend

# Instalar dependencias
npm install

# Instalar Bootstrap
npm install bootstrap@5.3 bootstrap-icons

# Iniciar servidor de desarrollo
npm start

# Compilar para producción
npm run build

# Previsualizar build de producción
npm run preview
```

### Tests y Lint

```bash
# Ejecutar tests unitarios
npm test

# Ejecutar tests e2e
npm run e2e

# Ejecutar linter
npm run lint

# Aplicar auto-fix al linter
npm run lint:fix
```

### PWA

```bash
# Agregar soporte PWA
ng add @angular/pwa

# Compilar PWA
npm run build:pwa
```

### Generación de Código

```bash
# Crear componente
ng generate component nombre-componente

# Crear servicio
ng generate service nombre-servicio

# Crear guard
ng generate guard nombre-guard

# Crear pipe
ng generate pipe nombre-pipe

# Crear interfaz
ng generate interface nombre-interfaz

# Crear módulo
ng generate module nombre-modulo
```

---

## 📝 Convenciones de Código

### Backend (PHP/Symfony)

**Comentarios**: En español  
**Código**: En inglés

```php
<?php

// Comentario en español sobre lo que hace el código
class ActivityService
{
    /**
     * Obtiene todas las actividades activas
     * 
     * @param array $filters Filtros opcionales
     * @return Activity[] Lista de actividades
     */
    public function getActiveActivities(array $filters = []): array
    {
        // Implementación en inglés
    }
}
```

**Estilo**: PSR-12

**Nombres**:
- Clases: PascalCase
- Métodos: camelCase
- Propiedades: camelCase
- Constantes: UPPER_SNAKE_CASE
- Interfaces: Nombre con sufijo "Interface"
- Excepciones: Nombre con sufijo "Exception"
- Repositorios: Nombre con sufijo "Repository"

### Frontend (TypeScript/Angular)

**Comentarios**: En español  
**Código**: En inglés

```typescript
// Comentario en español sobre el componente
@Component({
  selector: 'app-activity-list',
  templateUrl: './activity-list.component.html',
  styleUrls: ['./activity-list.component.scss']
})
export class ActivityListComponent implements OnInit {
  // Propiedades en inglés
  activities$: Observable<Activity[]>;
  loading = false;
  
  // Métodos en inglés
  ngOnInit(): void {
    // Implementation in English
  }
}
```

**Estilo**: Angular Style Guide

**Nombres**:
- Componentes: PascalCase
- Servicios: PascalCase con sufijo "Service"
- Interfaces: PascalCase con prefijo "I"
- Modelos: PascalCase con sufijo "Model"
- Pipes: PascalCase con sufijo "Pipe"
- Guards: PascalCase con sufijo "Guard"
- Directivas: PascalCase con prefijo "app"

---

## 🎨 Estilos con Bootstrap

### Clases de Bootstrap a Utilizar

```html
<!-- Contenedores -->
<div class="container">
<div class="container-fluid">
<div class="row">
<div class="col-12 col-md-6 col-lg-4">

<!-- Botones -->
<button class="btn btn-primary">
<button class="btn btn-secondary">
<button class="btn btn-success">
<button class="btn btn-danger">
<button class="btn btn-warning">
<button class="btn btn-light">
<button class="btn btn-dark">
<button class="btn btn-outline-primary">

<!-- Formularios -->
<div class="form-group">
<input class="form-control" type="text">
<select class="form-control">
<textarea class="form-control">

<!-- Alertas -->
<div class="alert alert-primary">
<div class="alert alert-success">
<div class="alert alert-danger">
<div class="alert alert-warning">

<!-- Tarjetas -->
<div class="card">
<div class="card-header">
<div class="card-body">
<div class="card-footer">

<!-- Tablas -->
<table class="table">
<table class="table table-striped">
<table class="table table-hover">
<table class="table table-bordered">

<!-- Utilidades -->
<div class="d-flex">
<div class="d-none d-md-block">
<div class="mt-3 mb-4 p-2">
```

---

## 📦 Paquetes Principales

### Backend (Composer)

```bash
# Symfony bundles ya instalados
symfony/orm-pack
symfony/validator
doctrine/annotations
symfony/messenger
symfony/http-client
aws/aws-sdk-php
lexik/jwt-authentication-bundle
symfony/security-bundle
```

### Frontend (NPM)

```bash
# Angular CLI
@angular/cli@latest

# Bootstrap
bootstrap@5.3
bootstrap-icons

# PWA
@angular/pwa

# RxJS
rxjs
```

---

## 🔐 Autenticación JWT

### Flujo de Autenticación

1. **Login**
   - POST `/api/auth/login`
   - Enviar: `{ "email": "...", "password": "..." }`
   - Recibir: `{ "token": "...", "refresh_token": "..." }`

2. **Refresh Token**
   - POST `/api/auth/refresh`
   - Enviar: `{ "refresh_token": "..." }`
   - Recibir: `{ "token": "...", "refresh_token": "..." }`

3. **Protected Routes**
   - Incluir header: `Authorization: Bearer <token>`

---

## 📊 Estructura de Base de Datos

### Tablas Principales

```sql
-- Usuarios
CREATE TABLE users (
    id UUID PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('ADMIN', 'COORDINATOR', 'TECHNICIAN', 'ATTENDEE'),
    name VARCHAR(255),
    phone VARCHAR(50),
    is_active BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME
);

-- Actividades
CREATE TABLE activities (
    id UUID PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    status ENUM('PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'),
    scheduled_start DATETIME,
    scheduled_end DATETIME,
    actual_start DATETIME,
    actual_end DATETIME,
    priority ENUM('LOW', 'MEDIUM', 'HIGH', 'URGENT'),
    location_address VARCHAR(500),
    created_by UUID,
    assigned_to UUID,
    created_at DATETIME,
    updated_at DATETIME
);

-- Asignaciones
CREATE TABLE assignments (
    id UUID PRIMARY KEY,
    activity_id UUID,
    technician_id UUID,
    assigned_by UUID,
    assigned_at DATETIME,
    notes TEXT,
    created_at DATETIME,
    updated_at DATETIME
);

-- Logs de actividades
CREATE TABLE activity_logs (
    id UUID PRIMARY KEY,
    activity_id UUID,
    user_id UUID,
    action ENUM('CREATED', 'UPDATED', 'STATUS_CHANGED', 'ASSIGNED', 'TIME_LOGGED'),
    old_value JSON,
    new_value JSON,
    created_at DATETIME
);

-- Notificaciones
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    user_id UUID,
    activity_id UUID,
    type ENUM('EMAIL', 'PUSH'),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN,
    sent_at DATETIME,
    created_at DATETIME
);
```

---

## 🔄 Estados de Actividades

### Estados del Ciclo de Vida

1. **PENDING** - Actividad pendiente de inicio
2. **IN_PROGRESS** - Actividad en curso (técnico inició)
3. **COMPLETED** - Actividad finalizada
4. **CANCELLED** - Actividad cancelada

### Prioridades

1. **LOW** - Baja prioridad
2. **MEDIUM** - Prioridad media (default)
3. **HIGH** - Alta prioridad
4. **URGENT** - Urgente

---

## 🌍 API Endpoints

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/refresh` - Refrescar token
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Obtener usuario actual

### Usuarios
- `GET /api/users` - Listar usuarios (Admin, Coordinator)
- `POST /api/users` - Crear usuario (Solo Admin)
- `GET /api/users/{id}` - Obtener usuario (Admin, Coordinator)
- `PUT /api/users/{id}` - Actualizar usuario (Admin: todo, Coordinator: limitado)
- `DELETE /api/users/{id}` - Eliminar usuario (Solo Admin)
- `PUT /api/users/{id}/toggle-active` - Activar/desactivar usuario (Solo Admin)

**Filtros para GET /api/users:**
- `role` - Filtrar por rol (ADMIN, COORDINATOR, TECHNICIAN, ATTENDEE)
- `isActive` - Filtrar por estado (true/false)
- `search` - Buscar por nombre o email

### Actividades ✅
- `GET /api/activities` - Listar actividades
- `POST /api/activities` - Crear actividad
- `GET /api/activities/{id}` - Obtener actividad
- `PUT /api/activities/{id}` - Actualizar actividad
- `DELETE /api/activities/{id}` - Eliminar actividad
- `POST /api/activities/{id}/start` - Iniciar actividad
- `POST /api/activities/{id}/complete` - Completar actividad
- `POST /api/activities/{id}/cancel` - Cancelar actividad

### Asignaciones ✅
- `GET /api/assignments` - Listar asignaciones (Admin, Coordinator, Technician)
- `POST /api/assignments` - Crear asignación (Admin, Coordinator)
- `GET /api/assignments/{id}` - Ver asignación (Admin, Coordinator, Technician: solo propias)
- `PUT /api/assignments/{id}` - Actualizar notas (Admin, Coordinator)
- `DELETE /api/assignments/{id}` - Eliminar asignación (Solo Admin)

**Filtros para GET /api/assignments:**
- `activity_id` - Filtrar por actividad
- `technician_id` - Filtrar por técnico
- `assigned_by` - Filtrar por quien asignó
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

---

## 📄 Formato de Commit

```
<tipo>: <descripción>

## Cambios
- Cambio 1
- Cambio 2

## Archivos modificados
- archivo1.php
- archivo2.ts
```

**Tipos**:
- `feat`: Nueva funcionalidad
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Cambios de formato (espacios, tabs, etc.)
- `refactor`: Refactorización de código
- `test`: Agregar o modificar tests
- `chore`: Tareas de mantenimiento

**Ejemplo**:
```
feat: agregar endpoint para crear actividades

## Cambios
- Implementar POST /api/activities
- Agregar validaciones con Symfony Validator
- Crear entity Activity con campos necesarios

## Archivos modificados
- backend/src/Controller/ActivityController.php
- backend/src/Entity/Activity.php
- backend/migrations/Version123456.php
```

---

## ⚠️ Notas Importantes

### Comentarios en Código
- TODOS los comentarios deben estar en **español**
- El código (nombres de variables, funciones, clases) debe estar en **inglés**

### Documentación
- Mantener este archivo actualizado
- Mantener DOCUMENTACION.md actualizado con cambios importantes

### Testing
- Escribir tests para nuevas funcionalidades
- Ejecutar tests antes de commitear

### Seguridad
- Nunca commitear credenciales o claves
- Usar variables de entorno para configuración sensible
- Validar todos los inputs en backend y frontend

---

## 🆘 Problemas Comunes

### Error de Permiso en WSL2
```bash
sudo chmod -R 755 var/
sudo chmod -R 755 storage/
```

### Error de Base de Datos SQLite
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:create
```

### Error de JWT
```bash
# Regenerar claves
openssl genrsa -passout pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -aes256 4096 -out config/jwt/private.pem
openssl rsa -passin pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

---

**Última actualización**: 21 de Enero de 2026
