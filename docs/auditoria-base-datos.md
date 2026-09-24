# Auditoría de base de datos — Agenda de Acuerdos

Fecha: 2026-09-21

## Alcance y fuentes

La fuente vigente auditada es la aplicación de producción ubicada en `D:\Sistemas\apps\agenda-acuerdos-lideres`, no la copia del repositorio. Se revisaron sus migraciones, modelos Eloquent, controladores, consultas y configuración. También se ejecutaron consultas estrictamente de solo lectura sobre las conexiones reales `agenda_acuerdos`, `auth_center` y `sistema_tickets`.

Se verificó que las 33 migraciones visibles están aplicadas. La inspección general con `db:show` fue bloqueada por una vista defectuosa de otra base (`cultura_organizacional.vista_resumen_global_anual`), por lo que el inventario y las pruebas de integridad se obtuvieron mediante consultas directas por tabla.

Ninguna tabla debe eliminarse sin respaldo, revisión de consumidores externos y una migración separada. La auditoría de `auth_center` y `sistema_tickets` se limita a los objetos consumidos por Agenda; no sustituye la revisión integral de esos sistemas.

## Resumen ejecutivo

El diseño funciona, pero actualmente reparte la misma realidad entre tres bases y conserva restos del esquema anterior:

1. Los usuarios viven en `auth_center.users`, mientras roles y permisos viven en la base local. Los IDs externos se guardan localmente sin claves foráneas.
2. `acuerdos.area`, `acuerdos.responsable`, `acuerdos.apoyo` y `kpis.category` son textos libres que duplican catálogos y personas.
3. Existen dos implementaciones de KPI: `kpis`/`kpi_valores` locales y `kpis`/`kpi_results` en `sistema_tickets`; el código activo usa la segunda.
4. La aplicación desactiva claves foráneas durante operaciones ordinarias del calendario. Esto permite insertar huérfanos y oculta inconsistencias de diseño.
5. Las tablas pivote del calendario carecen de restricciones únicas, por lo que admiten participantes o áreas duplicados.
6. `acuerdos` mezcla datos fuente con valores derivados (`porcentaje_avance` y métricas de tiempo), y usa el texto `ELIMINADO` como borrado lógico.
7. Hay una credencial de base de datos como valor predeterminado dentro de `config/database.php`. Debe rotarse y retirarse del código/historial como prioridad crítica.
8. La base real contiene 6 acuerdos con fechas inconsistentes, 21 acuerdos borrados mediante el marcador `area = 'ELIMINADO'` y 7 grupos de resultados KPI duplicados por período.
9. No se detectaron huérfanos en las relaciones revisadas hacia acuerdos, áreas ni Auth Center.

## Evidencia de producción

Conteos exactos obtenidos el 2026-09-21:

| Objeto | Filas | Observación |
|---|---:|---|
| `agenda_acuerdos.acuerdos` | 680 | 6 con secuencia de fechas inválida; 21 marcados como `ELIMINADO` |
| `agenda_acuerdos.areas` | 11 | Sin nombres duplicados normalizados |
| `agenda_acuerdos.bitacoras` | 4,295 | Sin huérfanos de acuerdo ni usuario externo |
| `agenda_acuerdos.comentarios` | 1,272 | Sin huérfanos de acuerdo ni usuario externo |
| `agenda_acuerdos.avances_diarios` | 2,728 | Sin huérfanos de acuerdo |
| `agenda_acuerdos.events` | 650 | Sin creadores inexistentes en Auth Center |
| `agenda_acuerdos.event_area` | 2,194 | Sin duplicados ni huérfanos |
| `agenda_acuerdos.event_user` | 0 | La función existe en código, pero no tiene uso persistido actual |
| Roles / permisos | 3 / 15 | 22 asignaciones de rol y 18 permisos directos; sin usuarios huérfanos |
| `auth_center.users` | 123 | Fuente real de identidad |
| `auth_center.departments` | 19 | Catálogo organizacional externo |
| `auth_center.systems` | 14 | Catálogo de sistemas externo |
| `auth_center.user_system_access` | 498 | Sin parejas usuario/sistema duplicadas |
| `auth_center.login_logs` | 129 | Auditoría central de accesos |
| `sistema_tickets.kpis` | 106 | 14 categorías textuales |
| `sistema_tickets.kpi_results` | 1,950 | 7 grupos duplicados por `(kpi_id, year, month, semana)` |

Otros resultados:

- `kpis`, `kpi_valores`, `personal_access_tokens`, `failed_jobs` y `agenda_user_profiles` existen localmente pero tienen 0 filas.
- `users` y `password_reset_tokens` locales no existen, aunque sus migraciones figuran como ejecutadas: existe deriva entre historial de migraciones y esquema real.
- `agenda_user_profiles` no aparece en el código fuente ni en las migraciones visibles. Su estructura intenta relacionar `auth_user_id` y `legacy_user_id`, pero está vacía.
- Hay una sola denominación de área usada por acuerdos que no coincide con el catálogo `areas`.
- `acuerdos.comentarios` todavía contiene datos en 13 acuerdos; no debe eliminarse hasta migrarlos o confirmar que son legado descartable.

## Inventario lógico

### Base principal de Agenda

| Tabla | Función | Estado observado |
|---|---|---|
| `acuerdos` | Acuerdos, estado y métricas acumuladas | Activa, requiere normalización |
| `bitacoras` | Auditoría de cambios de acuerdos | Activa |
| `comentarios` | Conversación/actividad de acuerdos | Activa |
| `avances_diarios` | Foto diaria del avance | Activa |
| `areas` | Catálogo local de áreas y color | Activa, sin unicidad en `name` |
| `events` | Calendario | Activa |
| `event_user` | Participantes de eventos | Activa, sin FK a usuario y sin unicidad |
| `event_area` | Áreas involucradas en eventos | Activa, sin unicidad |
| `permissions`, `roles` y pivotes Spatie | Autorización propia de Agenda | Activas |
| `users` local | Esquema anterior de autenticación | No existe en producción; retirar de la línea base nueva |
| `agenda_user_profiles` | Puente de identidad aparentemente experimental | Existe vacía y sin consumidor visible |
| `kpis`, `kpi_valores` locales | Implementación local anterior de KPI | Existen vacías; candidatas fuertes a retiro |
| `password_reset_tokens` local | Recuperación del esquema local | No existe en producción; retirar de la línea base nueva |
| `personal_access_tokens` | Tokens Sanctum | Existe vacía; condicional |
| `failed_jobs` | Fallos de colas Laravel | Existe vacía; conservar si hay workers/colas |

### Auth Center

| Tabla observada | Función |
|---|---|
| `users` | Identidad, contraseña, estado y departamento |
| `departments` | Catálogo organizacional |
| `systems` | Catálogo de aplicaciones |
| `user_system_access` | Acceso de un usuario a una aplicación |
| `login_logs` | Bitácora central de accesos |

### Sistema de tickets/KPI

| Tabla observada | Función |
|---|---|
| `kpis` | Definición de KPI (`code`, `name`, `category`, metas, estado) |
| `kpi_results` | Resultados mensuales y semanales |

## Diagrama actual reconstruido

Las líneas punteadas representan referencias lógicas sin clave foránea verificable.

```mermaid
erDiagram
    AUTH_USERS {
        bigint id PK
        bigint department_id FK
        string name
        string email UK
        boolean is_active
    }
    AUTH_DEPARTMENTS {
        bigint id PK
        string name
    }
    AUTH_SYSTEMS {
        bigint id PK
        string code UK
    }
    AUTH_USER_SYSTEM_ACCESS {
        bigint user_id FK
        bigint system_id FK
        boolean is_active
    }
    AUTH_LOGIN_LOGS {
        bigint user_id
        string system_code
        string status
    }

    AREAS {
        bigint id PK
        string name
        string color
    }
    ACUERDOS {
        bigint id PK
        string area
        string responsable
        string apoyo
        string estatus
        date fecha_inicio
        date fecha_compromiso
        int porcentaje_avance
    }
    BITACORAS {
        bigint id PK
        bigint acuerdo_id FK
        bigint user_id
        string usuario
        string campo
    }
    COMENTARIOS {
        bigint id PK
        bigint acuerdo_id FK
        bigint user_id
        string usuario
    }
    AVANCES_DIARIOS {
        bigint id PK
        bigint acuerdo_id FK
        date fecha
        int porcentaje_avance
    }
    EVENTS {
        bigint id PK
        bigint area_id FK
        bigint created_by
        datetime start
        datetime end
        datetime deleted_at
    }
    EVENT_USER {
        bigint event_id FK
        bigint user_id
    }
    EVENT_AREA {
        bigint event_id FK
        bigint area_id FK
    }

    ROLES {
        bigint id PK
        string name
    }
    PERMISSIONS {
        bigint id PK
        string name
    }
    MODEL_HAS_ROLES {
        bigint role_id FK
        bigint model_id
        string model_type
    }
    MODEL_HAS_PERMISSIONS {
        bigint permission_id FK
        bigint model_id
        string model_type
    }
    ROLE_HAS_PERMISSIONS {
        bigint role_id FK
        bigint permission_id FK
    }

    TICKET_KPIS {
        bigint id PK
        string code
        string category
        boolean is_active
    }
    TICKET_KPI_RESULTS {
        bigint id PK
        bigint kpi_id FK
        int year
        int month
        int semana
        text inicio_semana
        decimal value
    }

    AUTH_DEPARTMENTS ||--o{ AUTH_USERS : agrupa
    AUTH_USERS ||--o{ AUTH_USER_SYSTEM_ACCESS : recibe
    AUTH_SYSTEMS ||--o{ AUTH_USER_SYSTEM_ACCESS : habilita
    AUTH_USERS ||--o{ AUTH_LOGIN_LOGS : genera

    ACUERDOS ||--o{ BITACORAS : registra
    ACUERDOS ||--o{ COMENTARIOS : recibe
    ACUERDOS ||--o{ AVANCES_DIARIOS : mide
    AREAS ||--o{ EVENTS : area_principal
    EVENTS ||--o{ EVENT_USER : invita
    EVENTS ||--o{ EVENT_AREA : involucra
    AREAS ||--o{ EVENT_AREA : participa

    AUTH_USERS ||..o{ BITACORAS : actor_user_id
    AUTH_USERS ||..o{ COMENTARIOS : actor_user_id
    AUTH_USERS ||..o{ EVENTS : created_by
    AUTH_USERS ||..o{ EVENT_USER : participante
    AUTH_USERS ||..o{ MODEL_HAS_ROLES : autorizado
    AUTH_USERS ||..o{ MODEL_HAS_PERMISSIONS : autorizado

    ROLES ||--o{ MODEL_HAS_ROLES : asigna
    PERMISSIONS ||--o{ MODEL_HAS_PERMISSIONS : asigna
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : contiene
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : pertenece

    TICKET_KPIS ||--o{ TICKET_KPI_RESULTS : produce
```

## Tablas prescindibles o consolidables

“Prescindible” significa candidata después de validar datos, dependencias externas y respaldo; no es una orden de borrado.

| Tabla | Confianza | Propuesta | Condición previa |
|---|---:|---|---|
| `users` de la base local | Confirmado ausente | Retirar la migración de una futura línea base consolidada; no hay tabla que eliminar hoy | Mantener compatibilidad del historial de migraciones existente |
| `password_reset_tokens` local | Confirmado ausente | Igual que `users`; Auth Center es el dueño | Verificar el broker de contraseñas en todos los entornos |
| `agenda_user_profiles` | Alta | Eliminar | Está vacía y no tiene referencias en fuente; confirmar que ningún proceso externo la consume |
| `kpis` local | Muy alta | Eliminar | Está vacía; el código activo usa `sistema_tickets.kpis` |
| `kpi_valores` local | Muy alta | Eliminar junto con `kpis` local | Está vacía; respaldar DDL antes del retiro |
| `personal_access_tokens` | Media-alta | Eliminar si Agenda no ofrece API | Está vacía; confirmar consumidores externos antes de retirar `HasApiTokens` |
| `event_user` | Media | Mantener por ahora o retirar junto con la función de invitados | Está vacía, pero el controlador todavía sincroniza participantes |
| `failed_jobs` | Baja | Conservar por defecto | Está vacía; eliminar solo si se descartan colas persistentes en todos los entornos |

No son prescindibles: `bitacoras`, `comentarios`, `avances_diarios`, `event_user` y `event_area`; todas tienen uso activo. `events.area_id` podría consolidarse con `event_area`, pero primero debe definirse si representa al área organizadora. Si esa semántica existe, debe renombrarse a `organizer_area_id`, no eliminarse.

Columnas candidatas a retiro o rediseño:

- `acuerdos.comentarios`: no se observó lectura/escritura funcional, pero conserva datos en 13 acuerdos. Migrar esos valores a `comentarios` con marca de origen antes de retirarla.
- `acuerdos.area`: reemplazar por `area_id`.
- `acuerdos.responsable` y `acuerdos.apoyo`: reemplazar por una relación de participantes.
- `acuerdos.tiempo_total_dias`, `tiempo_detenido_dias`, `tiempo_efectivo_dias`: son datos derivados. Calcularlos desde transiciones o mantenerlos como snapshot explícito con una política de recálculo.
- `kpi_results.inicio_semana`: cambiar de `TEXT` a `DATE`.

## Hallazgos y recomendaciones

### P0 — Seguridad e integridad

1. Rotar inmediatamente la credencial expuesta como valor predeterminado en `config/database.php`, sustituirla por `env('DB_AREASKPI_PASSWORD')` sin secreto predeterminado y revisar el historial Git.
2. No ejecutar `Schema::disableForeignKeyConstraints()` en solicitudes normales. En una conexión persistente, una excepción puede dejar la sesión sin validación y permitir huérfanos.
3. Unificar el acceso a Auth Center. Actualmente `App\Models\User` usa la conexión `mysql` con tabla calificada `auth_center.users`, mientras `AuthUser` usa `mysql_auth`. Esto presupone que ambas bases viven en el mismo servidor y puede fallar si los hosts difieren.
4. Tratar el ID de Auth Center como identificador externo estable. Si ambas bases no comparten servidor/gestión transaccional, no intentar una FK física: usar integridad por aplicación, reconciliación periódica y eventos de baja/actualización.

### P1 — Normalización e identidad

1. Crear `area_id` en `acuerdos` y migrar por un alias normalizado. Añadir `UNIQUE areas(name)` o, preferiblemente, `areas(code)` único e inmutable.
2. Reemplazar el mapa de nombres de departamento codificado en `User::getAreaAttribute()` por una tabla `department_area_map(department_id, area_id)`.
3. Modelar responsables y apoyos con `acuerdo_participants(acuerdo_id, auth_user_id, role, display_name_snapshot)`. Restricción única sugerida: `(acuerdo_id, auth_user_id, role)`.
4. Mantener `usuario` en comentarios/bitácoras solo como `actor_name_snapshot`. No es redundancia accidental: preserva el nombre histórico si el usuario cambia o desaparece. Renombrarlo aclara su intención.
5. Reemplazar el borrado lógico `area = 'ELIMINADO'` con `deleted_at` en `acuerdos`. El área nunca debe representar el estado de eliminación.
6. Crear un historial explícito `acuerdo_status_history(acuerdo_id, from_status, to_status, reason, changed_by, changed_at)`. La bitácora genérica puede continuar para otros campos.

### P1 — KPI

1. Elegir un solo dueño del dominio KPI. Según el código actual debe ser `sistema_tickets`, o extraerse a un servicio/base KPI; la implementación local está abandonada.
2. Sustituir `kpis.category` por `area_id` o por un `area_code` compartido. Los `LIKE` sobre categorías impiden integridad y degradan índices.
3. Evitar mezclar granularidad mensual y semanal mediante nulos ambiguos. Diseño recomendado: una fila por período con `period_type`, `period_start`, `period_end`, o tablas separadas si las métricas difieren.
4. Agregar una unicidad real por granularidad. Ejemplo lógico semanal: `(kpi_id, period_type, period_start)`; mensual: la misma clave con el primer día del mes.
5. Usar `DECIMAL`, no `DOUBLE`, para metas y valores porcentuales que requieren comparaciones estables.

### P2 — Restricciones e índices

Restricciones recomendadas:

- `areas(code)` y `areas(name)` únicos.
- `event_user(event_id, user_id)` único; puede usar esa pareja como PK y prescindir de `id`.
- `event_area(event_id, area_id)` único; puede usar esa pareja como PK.
- `events`: `end >= start`.
- `acuerdos`: `porcentaje_avance BETWEEN 0 AND 100`, `fecha_compromiso >= fecha_inicio` y `fecha_cierre IS NULL OR fecha_cierre >= fecha_inicio`.
- `avances_diarios`: ya tiene la unicidad correcta `(acuerdo_id, fecha)`; añadir el check de porcentaje.
- `user_system_access(user_id, system_id)` único en Auth Center, si aún no existe.

Índices sugeridos a confirmar con `EXPLAIN` y volumen real:

| Tabla | Índice sugerido | Motivo |
|---|---|---|
| `acuerdos` | `(area_id, estatus, fecha_compromiso)` | filtros del tablero y seguimiento por área |
| `acuerdos` | `(responsable_user_id, estatus, fecha_cierre)` | estadísticas por responsable; omitir si se migra a pivote y ponerlo allí |
| `bitacoras` | `(acuerdo_id, campo, created_at)` | reconstrucción de transiciones |
| `comentarios` | `(acuerdo_id, created_at)` | últimos comentarios y cálculo de actividad |
| `avances_diarios` | `(fecha, acuerdo_id)` adicional | reportes transversales por fecha |
| `events` | `(start, end)` y `(created_by, start)` | rango de calendario y eventos propios |
| `event_user` | `(user_id, event_id)` además de la unicidad inversa | eventos visibles por usuario |
| `event_area` | `(area_id, event_id)` además de la unicidad inversa | eventos visibles por área |
| `model_has_roles` | `(model_type, model_id)` ya existe; revisar orden según consultas | autorización por usuario |
| `kpi_results` | `(kpi_id, year, month, semana)` temporal | consultas actuales; sustituir por clave de período normalizada |

No conviene crear todos los índices a ciegas: primero ejecutar `EXPLAIN ANALYZE`, revisar selectividad y evitar duplicar prefijos cubiertos por otros índices.

## Modelo objetivo propuesto

```mermaid
erDiagram
    AUTH_USER ||--o{ USER_SYSTEM_ACCESS : accede
    SYSTEM ||--o{ USER_SYSTEM_ACCESS : autoriza
    DEPARTMENT ||--o{ AUTH_USER : pertenece
    DEPARTMENT ||--o{ DEPARTMENT_AREA_MAP : traduce
    AREA ||--o{ DEPARTMENT_AREA_MAP : corresponde

    AREA ||--o{ AGREEMENT : clasifica
    AGREEMENT ||--o{ AGREEMENT_PARTICIPANT : asigna
    AUTH_USER ||..o{ AGREEMENT_PARTICIPANT : participa
    AGREEMENT ||--o{ AGREEMENT_COMMENT : recibe
    AUTH_USER ||..o{ AGREEMENT_COMMENT : escribe
    AGREEMENT ||--o{ AGREEMENT_AUDIT : audita
    AUTH_USER ||..o{ AGREEMENT_AUDIT : modifica
    AGREEMENT ||--o{ AGREEMENT_STATUS_HISTORY : transiciona
    AGREEMENT ||--o{ AGREEMENT_PROGRESS_DAILY : mide

    AREA ||--o{ EVENT : organiza
    EVENT ||--o{ EVENT_AREA : involucra
    AREA ||--o{ EVENT_AREA : participa
    EVENT ||--o{ EVENT_PARTICIPANT : invita
    AUTH_USER ||..o{ EVENT_PARTICIPANT : asiste

    AREA ||--o{ KPI : posee
    KPI ||--o{ KPI_RESULT : mide

    AUTH_USER {
        bigint id PK
        bigint department_id FK
        string email UK
    }
    AREA {
        bigint id PK
        string code UK
        string name UK
        string color
    }
    AGREEMENT {
        bigint id PK
        bigint area_id FK
        tinyint quadrant
        string status
        decimal progress_pct
        datetime deleted_at
    }
    AGREEMENT_PARTICIPANT {
        bigint agreement_id FK
        bigint auth_user_id
        string role
        string display_name_snapshot
    }
    KPI_RESULT {
        bigint kpi_id FK
        string period_type
        date period_start
        date period_end
        decimal value
    }
```

La línea punteada conceptual hacia `AUTH_USER` significa que los IDs pertenecen a otro límite de datos. Se deben resolver mediante un adaptador/repositorio de identidad, no mediante múltiples modelos contradictorios.

## Propuesta de relación con el nuevo sistema de usuarios

Arquitectura recomendada: Auth Center es la única fuente de identidad y credenciales; Agenda es dueña de sus roles, permisos y datos de negocio.

1. Identidad: todas las referencias locales guardan `auth_user_id` con el mismo tipo que `auth_center.users.id`.
2. Acceso: `auth_center.user_system_access` responde si la persona puede entrar a Agenda.
3. Autorización: roles/permisos específicos permanecen en Agenda, vinculados al `auth_user_id` externo.
4. Organización: `department_area_map` traduce departamentos de Auth Center a áreas de Agenda; se eliminan excepciones por correo y cadenas codificadas.
5. Historial: comentarios, auditorías y eventos guardan `auth_user_id` más un nombre snapshot cuando sea necesario conservar evidencia histórica.
6. Bajas: al revocar acceso, no borrar referencias históricas. Desactivar acceso y limpiar asignaciones operativas futuras mediante un job/evento idempotente.
7. Consistencia: ejecutar una reconciliación diaria que reporte IDs externos inexistentes, usuarios inactivos aún asignados y usuarios con rol local pero sin `user_system_access` activo.

Si las bases están en el mismo servidor MySQL y bajo el mismo ciclo de despliegue, pueden evaluarse FK entre esquemas. Si están en servidores distintos, las FK no son posibles y la estrategia correcta es consistencia eventual, monitoreo y claves naturales/snapshots solo para recuperación, nunca duplicar contraseñas.

## Plan de migración sin interrupción

1. Respaldar y obtener métricas reales; ejecutar las consultas de diagnóstico.
2. Rotar el secreto expuesto y corregir la configuración de conexiones.
3. Crear columnas/relaciones nuevas nullable (`area_id`, participantes, actor IDs claros) e índices únicos en pivotes después de deduplicar.
4. Poblar en segundo plano y generar reporte de valores sin correspondencia.
5. Hacer escritura dual temporal y comparar resultados.
6. Cambiar lecturas al modelo normalizado y vigilar errores/rendimiento.
7. Aplicar `NOT NULL`, checks y unicidades cuando no haya huérfanos.
8. Dejar una versión completa de respaldo y un período de observación.
9. Retirar columnas/tablas antiguas en una migración separada y reversible mediante restauración de respaldo, no mezclada con el backfill.

## Consultas de validación antes de eliminar o restringir

Adaptar nombres de esquema antes de ejecutar.

```sql
-- Inventario y tamaño real
SELECT table_schema, table_name, table_rows,
       ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
FROM information_schema.tables
WHERE table_schema IN ('agenda_acuerdos', 'auth_center', 'sistema_tickets')
ORDER BY table_schema, table_name;

-- Tablas locales candidatas: contenido antes de DROP
SELECT 'users' AS tabla, COUNT(*) AS filas FROM users
UNION ALL SELECT 'kpis', COUNT(*) FROM kpis
UNION ALL SELECT 'kpi_valores', COUNT(*) FROM kpi_valores
UNION ALL SELECT 'password_reset_tokens', COUNT(*) FROM password_reset_tokens
UNION ALL SELECT 'personal_access_tokens', COUNT(*) FROM personal_access_tokens;

-- Áreas duplicadas por mayúsculas/espacios
SELECT UPPER(TRIM(name)) AS area_normalizada, COUNT(*)
FROM areas
GROUP BY UPPER(TRIM(name))
HAVING COUNT(*) > 1;

-- Textos de acuerdo que no resuelven al catálogo de áreas
SELECT DISTINCT a.area
FROM acuerdos a
LEFT JOIN areas ar ON UPPER(TRIM(ar.name)) = UPPER(TRIM(a.area))
WHERE ar.id IS NULL;

-- Pivotes duplicados
SELECT event_id, user_id, COUNT(*) n
FROM event_user GROUP BY event_id, user_id HAVING n > 1;

SELECT event_id, area_id, COUNT(*) n
FROM event_area GROUP BY event_id, area_id HAVING n > 1;

-- Huérfanos locales
SELECT b.id FROM bitacoras b LEFT JOIN acuerdos a ON a.id = b.acuerdo_id WHERE a.id IS NULL;
SELECT c.id FROM comentarios c LEFT JOIN acuerdos a ON a.id = c.acuerdo_id WHERE a.id IS NULL;
SELECT d.id FROM avances_diarios d LEFT JOIN acuerdos a ON a.id = d.acuerdo_id WHERE a.id IS NULL;
SELECT eu.event_id FROM event_user eu LEFT JOIN events e ON e.id = eu.event_id WHERE e.id IS NULL;
SELECT ea.event_id FROM event_area ea LEFT JOIN events e ON e.id = ea.event_id WHERE e.id IS NULL;

-- IDs de usuario externos inexistentes (solo si ambos esquemas son accesibles desde la conexión)
SELECT b.user_id FROM bitacoras b
LEFT JOIN auth_center.users u ON u.id = b.user_id
WHERE b.user_id IS NOT NULL AND u.id IS NULL;

SELECT e.created_by FROM events e
LEFT JOIN auth_center.users u ON u.id = e.created_by
WHERE u.id IS NULL;

SELECT eu.user_id FROM event_user eu
LEFT JOIN auth_center.users u ON u.id = eu.user_id
WHERE u.id IS NULL;

-- Valores inválidos que bloquearían checks
SELECT id, porcentaje_avance FROM acuerdos
WHERE porcentaje_avance NOT BETWEEN 0 AND 100;

SELECT id, fecha_inicio, fecha_compromiso, fecha_cierre FROM acuerdos
WHERE fecha_compromiso < fecha_inicio
   OR (fecha_cierre IS NOT NULL AND fecha_cierre < fecha_inicio);
```

## Criterio de cierre de la auditoría

La auditoría ya incluye esquema de código, conteos y verificaciones de integridad sobre producción. Antes de emitir migraciones destructivas todavía deben obtenerse y conservarse el DDL real (`SHOW CREATE TABLE`), la lista completa de índices, planes `EXPLAIN ANALYZE`, versión de MySQL, consumidores externos y políticas de respaldo/retención. También deben corregirse los 6 acuerdos con fechas inválidas, revisar el área sin correspondencia y deduplicar los 7 períodos KPI.
