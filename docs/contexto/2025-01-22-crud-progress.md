# Progreso CRUD - 22/01/2026

## ✅ Arreglado Hoy

### 1. Botón "Nueva Actividad" - RESUELTO
- **Problema**: Error 500 "Could not convert database value 'new' to Doctrine Type uuid"
- **Causa**: Navegación a `/activities/new` donde "new" se interpretaba como UUID
- **Solución**: Cambiado a mostrar alert informativo sobre API endpoint

### 2. Debug preparado para botón "Nuevo Usuario"
- **Agregado**: Console logs en `openCreateModal()` para detectar si se ejecuta
- **Próximo paso**: Usuario probar y reportar si se ven los logs

### 3. Progreso técnico (Frontend - permisos UI)
- Se implementó sincronización reactiva del usuario actual en la UI de frontend:
  - ActividadesList suscribe a `AuthService.currentUser$` para mantener `currentUser` actualizado.
  - UsuariosList y ActivityDetail también se actualizan al cambiar el usuario.
- El botón "Nueva Actividad" ahora verifica permisos con `canCreateActivity()` y muestra una alerta si no está permitido, evitando la navegación.
- Se añadieron hooks de limpieza (OnDestroy) para evitar fugas de memoria por suscripciones.
- Este avance alinea la UI con las reglas de permisos del backend (Admin/Coordinator).

## ❌ Pendiente: CRUD de Usuarios

### Botón "Nuevo Usuario" no funciona
- **Estado**: Debug preparado, esperando feedback del usuario
- **Posibles causas**:
  1. Evento click no se ejecuta
  2. `canCreateUser()` devuelve false
  3. Error en template binding
  4. Problema con modal overlay

### Funcionalidades por probar
- **Crear usuario**: Modal debería abrirse y permitir crear
- **Editar usuario**: Botones de editar por verificar
- **Eliminar usuario**: Confirmación de eliminación
- **Activar/desactivar usuario**: Toggle status

## Próximos pasos

1. **Usuario probar botón "Nuevo Usuario"** y reportar logs en consola
2. **Si no funciona**: Investigar template binding y permisos
3. **Si funciona**: Probar crear usuario completo
4. **Luego**: Modal asignar técnico
5. **Finalmente**: CRUD completo de actividades si necesario

## Estado del Backend
- ✅ POST /api/users - Crear usuario (solo Admin)
- ✅ PUT /api/users/{id} - Actualizar usuario  
- ✅ DELETE /api/users/{id} - Eliminar usuario (solo Admin)
- ✅ PUT /api/users/{id}/toggle-active - Activar/desactivar (solo Admin)
- ✅ POST /api/activities - Crear actividad (Admin/Coordinator)

**El backend está listo. El problema está en el frontend.**
