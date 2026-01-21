# Sistema de Seguimiento de Técnicos en Campo

Sistema web y móvil (PWA) para la gestión y seguimiento de técnicos en campo.

## 📌 Descripción

Este sistema permite optimizar la coordinación, registrar tiempos reales de trabajo, mejorar la trazabilidad, centralizar información y reducir tiempos muertos y reclamos.

### Características Principales
- ✅ Autenticación con roles (Admin, Coordinador, Técnico, Acudiente)
- ✅ CRUD de actividades y asignaciones
- ✅ Control de estados y tiempos (programado vs real)
- ✅ Registro de inicio y fin de tareas
- ✅ Observaciones
- ✅ Notificaciones por email (Amazon SES)
- ✅ Seguridad mediante HTTPS, JWT y roles
- ✅ Aplicación responsiva y PWA
- ✅ Historial de acciones

## 🏗️ Stack Tecnológico

### Backend
- **Framework**: Symfony 7.4
- **PHP**: 8.2.30
- **Base de Datos**: SQLite (dev) / MySQL (prod)
- **ORM**: Doctrine ORM
- **Autenticación**: JWT (lexik/jwt-authentication-bundle)
- **Email**: AWS SDK PHP (Amazon SES)
- **Mensajería**: Symfony Messenger

### Frontend
- **Framework**: Angular 17
- **Estilos**: Bootstrap 5.3 + SCSS
- **PWA**: Angular Service Worker
- **State Management**: Signals + RxJS

## 🚀 Inicio Rápido

### Prerrequisitos
- PHP 8.2+
- Composer 2.x
- Node.js 20+
- npm 10+
- Git 2.x

### Instalación Backend

```bash
cd backend
composer install
php bin/console doctrine:migration:migrate
php -S localhost:8000 -t public
```

### Instalación Frontend

```bash
cd frontend
npm install
npm install bootstrap@5.3 bootstrap-icons
npm start
```

## 📖 Documentación

Para más detalles, consulte [DOCUMENTACION.md](docs/DOCUMENTACION.md)

## 📁 Estructura del Proyecto

```
seguimiento_tecnicos/
├── backend/          # Backend Symfony 7
├── frontend/         # Frontend Angular 17
├── docs/             # Documentación
├── .gitignore
└── README.md
```

## 🔐 Roles de Usuario

| Rol | Permisos |
|-----|----------|
| Administrador | Acceso total |
| Coordinador | Gestión de actividades y asignaciones |
| Técnico | Ver/actualizar asignaciones propias |
| Acudiente | Solo lectura |

## 📄 Licencia

Proprietary - Todos los derechos reservados.

---

**Desarrollado para**: Sistema de Seguimiento de Técnicos en Campo  
**Versión**: 1.0.0  
**Fecha**: 21 de Enero de 2026
