# 📑 Plan Maestro de Orquestación de IA

## Proyecto: Seguimiento de Técnicos en Campo (Angular + Symfony)

---

## 👤 El Orquestador (Humano)

**Misión:** Dirección estratégica, validación de lógica de negocio y control de calidad final.
**Responsabilidad:** Autorizar la integración de código en la rama `master` tras la validación de los agentes.

---

## 🏛️ Definición de Roles (Agentes)

### 1. Gemini-CLI | "The Architect & Auditor"

- **Rol:** Arquitecto Senior y Auditor de Código.
- **Misión:** Diseñar contratos de API, definir la estructura del sistema y auditar la calidad del código generado por otros agentes.
- **Instrucción Clave:** Debe usar su ventana de contexto de 1M+ para asegurar que el Backend y Frontend estén perfectamente sincronizados.

### 2. OpenCode CLI (GPT-5 Nano) | "The Builder"

- **Rol:** Implementador de Código de alto volumen.
- **Misión:** Generar Boilerplate, CRUDS, Entidades Symfony (PHP 8+), Componentes Angular y Servicios.
- **Instrucción Clave:** No se permite finalizar ninguna tarea sin su correspondiente test unitario o de integración.

### 3. Antigravity Agent | "The Live Copilot"

- **Rol:** Soporte en tiempo real y experto en entorno.
- **Misión:** Resolver errores de compilación, ajustar estilos SCSS/Bootstrap y gestionar problemas específicos de WSL2.
- **Instrucción Clave:** Actuar como "pegamento" para integrar los cambios de los CLIs en el código vivo del editor.

---

## 🛠️ Protocolo de Desarrollo (La Constitución)

1.  **API-First:** No se inicia ninguna funcionalidad sin un contrato JSON/OpenAPI definido por **Gemini-CLI**.
2.  **Pruebas como Juez:** El código sin tests (`.spec.ts` o `Test.php`) se considera incompleto y debe ser rechazado.
3.  **Clean Code en WSL2:** Seguir estándares PSR-12 para PHP y Angular Style Guide. Todo el código fuente (variables/funciones) debe estar en **inglés**.
4.  **Control de Deuda:** Antes de implementar, se debe consultar si existe lógica reutilizable para evitar redundancia.

---

## 🔄 Ciclo de Vida de una Tarea (Sprint)

1.  **Definición:** Gemini-CLI genera el contrato de API.
2.  **Ejecución:** OpenCode CLI construye la lógica basándose en el contrato.
3.  **Auditoría:** Gemini-CLI revisa el código generado.
4.  **Refinamiento:** Antigravity ajusta detalles visuales y resuelve errores de terminal en WSL2.

---

## 💡 Comandos de Refresco de Memoria

_Si un agente se desvía, pégale el párrafo correspondiente:_

> "RECUERDA TU ROL: Eres [Nombre del Rol]. Consulta el archivo `DEVELOPMENT_PLAN.md` para retomar tus directrices de arquitectura y protocolos de salida."
