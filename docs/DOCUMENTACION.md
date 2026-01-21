# Documentación - Sistema de Seguimiento de Técnicos en Campo

## 📌 Información General

**Nombre del Proyecto**: Sistema de Seguimiento de Técnicos en Campo  
**Versión**: 1.0.0  
**Fecha de Inicio**: 21 de Enero de 2026  
**Estado**: En Desarrollo - Fase 1 (Configuración Inicial)

---

## 📋 Descripción del Sistema

Sistema web y móvil (PWA) para la gestión y seguimiento de técnicos en campo. El objetivo principal es optimizar la coordinación, registrar tiempos reales de trabajo, mejorar la trazabilidad, centralizar información y reducir tiempos muertos y reclamos.

### Características Principales
- Autenticación con roles: Administrador, Coordinador, Técnico y Acudiente
- CRUD de actividades y asignaciones
- Control de estados y tiempos (programado vs real)
- Registro de inicio y fin de tareas
- Observaciones
- Notificaciones por email (Amazon SES)
- Seguridad mediante HTTPS, JWT y roles
- Aplicación responsiva y PWA
- Historial de acciones

---

## 🏗️ Stack Tecnológico

### Backend (Symfony 7)
- **Framework**: Symfony 7.4
- **PHP**: 8.2.30
- **Base de Datos**: SQLite (desarrollo) / MySQL (producción)
- **ORM**: Doctrine ORM
- **Autenticación**: JWT (lexik/jwt-authentication-bundle)
- **Seguridad**: Symfony Security Bundle
- **Email**: AWS SDK PHP (Amazon SES)
- **Mensajería**: Symfony Messenger
- **HTTP Client**: Symfony HTTP Client

### Frontend (Angular 17)
- **Framework**: Angular 17
- **TypeScript**: 5.2+
- **Estilos**: Bootstrap 5.3 + SCSS
- **PWA**: Angular Service Worker
- **State Management**: Signals + RxJS
- **HTTP**: HttpClient + Interceptors

### DevOps
- **OS**: Ubuntu 22.04.5 LTS (WSL2)
- **Git**: 2.34.1
- **Node.js**: 20.17.0
- **Composer**: 2.8.11
- **npm**: 10.8.2

---

## 📁 Estructura del Proyecto

```
seguimiento_tecnicos/
├── backend/                    # Backend Symfony 7
│   ├── bin/                   # Scripts ejecutables
│   ├── config/                # Configuración de Symfony
│   │   ├── jwt/              # Claves JWT
│   │   └── packages/         # Configuración de bundles
│   ├── migrations/           # Migraciones de base de datos
│   ├── public/               # Archivos públicos
│   ├── src/
│   │   ├── Controller/      # Controladores API
│   │   ├── Entity/           # Entidades Doctrine
│   │   ├── Repository/       # Repositorios personalizados
│   │   ├── Service/          # Servicios de negocio
│   │   ├── EventListener/    # Eventos y listeners
│   │   ├── Security/         # Configuración de seguridad
│   │   ├── DTO/              # Data Transfer Objects
│   │   └── Messenger/        # Mensajes para colas
│   ├── templates/            # Templates (si se usara)
│   ├── tests/                # Tests
│   ├── translations/         # Traducciones
│   ├── vendor/               # Dependencias de Composer
│   ├── .env                  # Variables de entorno
│   ├── composer.json         # Dependencias PHP
│   └── composer.lock         # Lock de dependencias
│
├── frontend/                  # Frontend Angular 17
│   ├── src/
│   │   ├── app/
│   │   │   ├── core/         # Módulo Core
│   │   │   │   ├── interceptors/
│   │   │   │   ├── guards/
│   │   │   │   ├── services/
│   │   │   │   ├── models/
│   │   │   │   └── constants/
│   │   │   ├── features/     # Módulos de características
│   │   │   │   ├── auth/
│   │   │   │   ├── activities/
│   │   │   │   ├── users/
│   │   │   │   └── dashboard/
│   │   │   ├── shared/       # Componentes compartidos
│   │   │   │   ├── components/
│   │   │   │   ├── directives/
│   │   │   │   └── pipes/
│   │   │   ├── app.component.*
│   │   │   └── app.config.ts
│   │   ├── assets/           # Imágenes, fuentes, etc.
│   │   ├── environments/     # Entornos (dev, prod)
│   │   └── styles/           # Estilos globales
│   ├── angular.json          # Configuración de Angular
│   ├── package.json          # Dependencias Node.js
│   ├── tsconfig.json         # Configuración TypeScript
│   ├── ngsw-config.json      # Configuración PWA
│   └── manifest.webmanifest  # Manifest de la PWA
│
├── docs/                      # Documentación
│   ├── DOCUMENTACION.md      # Esta documentación
│   ├── API.md                # Documentación de API
│   └── DIAGRAMAS.md          # Diagramas de arquitectura
│
├── .gitignore                 # Archivos ignorados por Git
└── README.md                  # Información del proyecto
```

---

## 🔧 Instalación y Configuración

### Prerrequisitos
- PHP 8.2+
- Composer 2.x
- Node.js 20+
- npm 10+
- Git 2.x
- Ubuntu 22.04 LTS (WSL2) o Linux similar

### Instalación del Backend

```bash
# Clonar el repositorio (cuando exista)
# git clone <repositorio> seguimiento_tecnicos
# cd seguimiento_tecnicos/backend

# Instalar dependencias de Composer
composer install

# Configurar variables de entorno
cp .env .env.local
# Editar .env.local con las credenciales necesarias

# Generar claves JWT
mkdir -p config/jwt
openssl genrsa -passout pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -aes256 4096 -out config/jwt/private.pem
openssl rsa -passin pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Ejecutar migraciones de base de datos
php bin/console doctrine:migration:migrate

# Iniciar servidor de desarrollo
php -S localhost:8000 -t public
```

### Instalación del Frontend

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
```

---

## 🔐 Configuración de Seguridad

### JWT Configuration
Las claves JWT se generan automáticamente y se almacenan en `backend/config/jwt/`.

### Variables de Entorno Requeridas
```bash
APP_ENV=dev
APP_SECRET=<secret_key>
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"
JWT_SECRET_KEY="%kernel.project_dir%/config/jwt/private.pem"
JWT_PUBLIC_KEY="%kernel.project_dir%/config/jwt/public.pem"
JWT_PASSPHRASE=f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5
```

### Configuración de AWS SES (Notificaciones Email)
```bash
AWS_ACCESS_KEY_ID=<aws_access_key>
AWS_SECRET_ACCESS_KEY=<aws_secret_key>
AWS_REGION=<aws_region>
SES_FROM_EMAIL=<from_email_address>
SES_FROM_NAME=<from_name>
```

---

## 📊 Roles y Permisos

| Rol | Permisos |
|-----|----------|
| **Administrador** | Acceso total a todas las funcionalidades |
| **Coordinador** | Gestión de actividades y asignaciones, asignar técnicos |
| **Técnico** | Ver y actualizar sus propias asignaciones, registrar tiempos |
| **Acudiente** | Solo lectura de asignaciones relacionadas |

---

## 📡 Endpoints de API (Planificado)

### Autenticación ✅
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/refresh` - Refrescar token (pendiente implementación)
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Obtener información del usuario actual

### Usuarios ✅
- `GET /api/users` - Listar usuarios (Admin, Coordinator)
- `POST /api/users` - Crear usuario (Solo Admin)
- `GET /api/users/{id}` - Obtener usuario (Admin, Coordinator)
- `PUT /api/users/{id}` - Actualizar usuario (Admin: todo, Coordinator: limitado)
- `DELETE /api/users/{id}` - Eliminar usuario (Solo Admin)
- `PUT /api/users/{id}/toggle-active` - Activar/desactivar usuario (Solo Admin)

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

## 🚀 Scripts Disponibles

### Backend (Composer)
```bash
# Ejecutar migraciones
php bin/console doctrine:migration:migrate

# Crear nueva migración
php bin/console doctrine:migration:diff

# Limpiar caché
php bin/console cache:clear

# Ver rutas disponibles
php bin/console debug:router
```

### Frontend (npm)
```bash
# Iniciar servidor de desarrollo
npm start

# Compilar para producción
npm run build

# Verificar TypeScript
npx tsc --noEmit

# Ejecutar tests
npm test

# Ejecutar lint
npm run lint

# PWA build
npm run build:pwa
```

---

## 🔄 Flujo de Trabajo de Desarrollo

1. **Configuración Inicial** ✅
    - ✅ Verificar herramientas disponibles
    - ✅ Crear estructura de directorios
    - ✅ Configurar Symfony 7 backend
    - ✅ Configurar Git
    - ✅ Crear documentación

2. **Fase 1: MVP Básico** (En progreso)
    - ✅ Implementar autenticación JWT
    - ✅ CRUD de usuarios (UserController)
    - ✅ CRUD de actividades (ActivityController)
    - ✅ Sistema de asignaciones (AssignmentController)
    - ✅ Validaciones Symfony Validator en entidades
    - ✅ Registro de tiempos (actividades)
    - 🔄 Notificaciones por email (NotificationService - pendiente)

3. **Fase 2: Dashboard y Reportes**
    - Dashboard con KPIs
    - Gráficos de tiempo real vs programado
    - Reportes por técnico
    - Reportes por período

4. **Fase 3: Fotos y Exportaciones**
    - Subida de imágenes
    - Galería de fotos
    - Exportación a CSV/PDF

5. **Fase 4: Notificaciones Push**
    - Web Push API
    - Notificaciones en tiempo real
   - Permisos de notificaciones

---

## 📝 Convenciones de Código

### Backend (PHP/Symfony)
- PSR-12 para estilo de código
- Comentarios en español
- Nombres de clases en PascalCase
- Nombres de métodos en camelCase
- Constantes en UPPER_SNAKE_CASE
- Interfaces terminan en "Interface"
- Excepciones terminan en "Exception"
- Repositorios terminan en "Repository"

### Frontend (TypeScript/Angular)
- Componentes: PascalCase
- Servicios: PascalCase con sufijo "Service"
- Interfaces: PascalCase con prefijo "I"
- Modelos: PascalCase con sufijo "Model"
- Comentarios en español
- Código en inglés

### Git
- Mensajes de commits en español
- Branch feature: `feature/nombre-caracteristica`
- Branch bugfix: `bugfix/descripcion-del-error`
- Branch hotfix: `hotfix/corresion-urgente`

---

## 🐛 Solución de Problemas Comunes

### Errores de conexión a la base de datos
```bash
# Verificar configuración en .env
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:create
```

### Errores de JWT
```bash
# Regenerar claves JWT
openssl genrsa -passout pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -aes256 4096 -out config/jwt/private.pem
openssl rsa -passin pass:f00e5ed50d5799e06b00269df46e622450999ec420b7b79096241d7ba76fa4e5 -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### Errores de permisos en WSL2
```bash
# Corregir permisos
sudo chmod -R 755 var/
```

---

## 📞 Contacto y Soporte

Para preguntas o problemas, contactar con el equipo de desarrollo.

---

## 📅 Historial de Cambios

| Fecha | Versión | Cambio | Autor |
|-------|---------|--------|-------|
| 21/01/2026 | 1.0.0 | Creación inicial del proyecto y documentación | - |

---

**Última actualización**: 21 de Enero de 2026
