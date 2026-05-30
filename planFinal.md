# Plan de Implementación Final: Sistema de Cuotas Personalizadas

**Proyecto:** Sistema de Cuotas Personalizadas para el Registro de Socios en Vista Verde Country Club.

**Fecha:** 2026-05-28

**Versión:** consolidada y actualizada con cambios de arquitectura aplicados.

---

## OBJETIVO GENERAL

Permitir que el sistema SISVV soporte la asignación de precios personalizados (descuentos o cuotas especiales) a los socios en sus mensualidades y cargos fijos, así como el manejo de múltiples membresías simultáneas por socio, optimizando el proceso de facturación mensual masiva.

---

## REPARTO DE RESPONSABILIDADES

| Acción | Departamento |
|---|---|
| Alta/edición de datos del socio | Recepción |
| Asignar/quitar membresías (alta tarifa base) | Recepción |
| Activar cargos fijos a tarifa base (locker, resguardo, etc.) | Recepción |
| Configurar `monto_personalizado` por socio | **Sistemas (exclusivo)** |
| Editar el catálogo base de cuotas (afecta a todos) | Sistemas |

---

## ARQUITECTURA DE TABLAS

| Tabla | Contenido | Rol |
|---|---|---|
| `socios_membresias` | 1 fila por socio: referencia a la membresía principal y su estado | Solo estado y referencia — **no es fuente de cobros** |
| `socios_cuotas` | **Todas** las membresías del socio (principal + adicionales) + cargos fijos | Fuente única de cobros y precios personalizados |
| `membresias` | Catálogo de membresías | `disponible` (boolean) — controla visibilidad en formularios |

> **Arquitectura objetivo (Fase 4.2):** `socios_cuotas` será la fuente única de verdad para todos los cobros. Actualmente la membresía principal solo existe en `socios_membresias`; al implementar la Fase 4.2 se duplicará también en `socios_cuotas`, permitiendo asignarle `monto_personalizado` como cualquier otra cuota.

**Principal = la membresía con el mayor `monto` base** (cuota tipo `MEN`) entre todas las del socio. Desempate: `created_at` asc, luego `id` asc.

La determinación de la principal es **automática e interna** — no existe UI para designarla manualmente.

---

## 1. ALINEACIÓN CON LOS REQUERIMIENTOS FUNCIONALES

### RF 1 — Múltiples membresías simultáneas
- La membresía de mayor monto va a `socios_membresias` (principal, con estado explícito).
- Las membresías adicionales van a `socios_cuotas`, vinculadas a la cuota del tipo deseado (MEN/INA/ANU). Su estado se deriva de `cuota->tipo`.
- Cargos fijos (locker, resguardo) también viven en `socios_cuotas` (cuotas sin `clave_membresia`).
- Un socio no puede tener dos veces exactamente la misma cuota (índice único `id_socio, id_cuota`).

### RF 2.1 — Mínimo una membresía obligatoria
Validación en `SocioForm` (`claves_membresia` required, min:1).

### RF 2.2 — Cuotas base definidas
Precio base en `cuotas.monto`. Aplica por defecto para socios sin `monto_personalizado`.

### RF 2.3 — Cuotas personalizadas
Columna `monto_personalizado` (decimal, nullable) en `socios_cuotas`.

> **Limitación actual (hasta Fase 4.2):** socios con una sola membresía la tienen únicamente en `socios_membresias`, que no tiene `monto_personalizado`. Al implementar la Opción B en Fase 4.2, la principal se duplicará en `socios_cuotas` y esta limitación desaparecerá.

### RF 2.4 — Independencia entre socios
`monto_personalizado` vive en la fila individual de `socios_cuotas`. No afecta a otros socios ni al catálogo.

### RF 2.5 — Prevención de cobros duplicados
La facturación iterará directamente por cada `SocioCuota` activa del socio:

```php
EstadoCuenta::where('id_socio', $socio->id)
    ->where('id_cuota', $socioCuota->id_cuota)
    ->whereYear('fecha', $fecha->year)
    ->whereMonth('fecha', $fecha->month)
    ->exists();
```

Si existe → omitir; si no → facturar con `$socioCuota->monto_a_cobrar`.

### RF 2.6 — Impedir membresías idénticas
Índice único compuesto `(id_socio, id_cuota)` en `socios_cuotas`.

### Estado de membresías
- **Principal** (`socios_membresias.estado`): MEN, INA, ANU, CAN.
- **Adicionales** (implícito en `cuota->tipo`): MEN, INA, ANU. No existe CAN en socios_cuotas — cancelar una membresía adicional elimina la fila.
- **Cancelación total**: si todas las membresías del socio se cancelan, se conserva la de mayor monto como CAN en `socios_membresias`.

### Membresías disponibles (`membresias.disponible`)
Columna boolean (default `true`). Las membresías `disponible=false` no aparecen en formularios de registro/edición de nuevos socios.

Marcadas como `false`: `CC-V-C`, `CC-V-S`, `CG-V-C`, `CG-V-S`, `COR`, `CUR`, `EST`, `EVE`, `INT`.

**Excepción en edición:** si un socio ya tiene asignada una membresía `disponible=false`, ésta sigue mostrándose en su formulario de edición para permitir su gestión.

### RF 3 — Configuración de membresías en Recepción
- **`SociosNuevo` y `SociosEditar`:** lista con scroll de todas las membresías disponibles. Cada membresía tiene un **dropdown de estado** (Seleccionar / Activa / Inactiva / Cancelada). La opción "Seleccionar" (vacío) significa no asignada. Anual solo aparece si la membresía ya está en estado ANU.
- `estados_membresia` (mapa clave→estado) es la fuente de verdad; `claves_membresia` se deriva de él.
- **No existe UI** para seleccionar membresía principal — el sistema la determina por mayor monto.

### RF 4 — Precios personalizados en facturación
`SocioCuota->monto_a_cobrar` devuelve `monto_personalizado ?? cuota->monto`. Toda facturación usa este accessor.

### RF 5 — Reportes actualizados
- `SociosExport`: usa `monto_a_cobrar`. Columna "TARIFA PERSONALIZADA" (SÍ/NO).
- `CarteraVencidaExport`: lista todas las membresías separadas por comas. Filtra cancelados verificando que **todas** las membresías del socio estén CAN.

### RF 6 — Pórtico de Acceso
- Lista principal (desde `socios_membresias`) + adicionales (desde `cuotasMembresia`).
- **Acceso permitido** si la principal existe y `estado !== 'CAN'`, o si existe al menos una membresía adicional.
- **Acceso denegado** si la única membresía es CAN, o no hay membresías.

### RF 7 — Actualización masiva de cuotas base
Al modificar `cuotas.monto` desde Sistemas, el cambio aplica automáticamente en el siguiente ciclo de facturación para socios sin `monto_personalizado`.

### RF 8 — Diferenciación visual de tarifa especial
- Badge en Estados de Cuenta si el socio tiene al menos una `monto_personalizado IS NOT NULL` en `socios_cuotas`.
- Filtro toggle para listar solo socios con tarifa especial.

> **Limitación actual:** el badge solo aparece para socios con membresías adicionales en `socios_cuotas`. Socios con una sola membresía (principal en `socios_membresias`) no mostrarán el badge hasta que se implemente la Fase 4.2 (Opción B).

---

## 2. PLAN DE IMPLEMENTACIÓN POR FASES

### FASE 1 — Migración Estructural ✅ COMPLETADA

- `socios_cuotas`: `id_socio` e `id_cuota` a `unsignedInteger`, columna `monto_personalizado`, índice único `(id_socio, id_cuota)`, FK con cascadeOnDelete/nullOnDelete.
- Columna `estado` en `socios_cuotas`: **agregada y luego eliminada** — el estado de adicionales queda implícito en `cuota->tipo`.
- Columna `disponible` (boolean, default `true`) en `membresias`. Nueve membresías marcadas `false`.

---

### FASE 2 — Modelos ✅ COMPLETADA

- **`SocioCuota`**: `monto_personalizado` en `$fillable`. Accessor `monto_a_cobrar`. Relación `cuota()`.
- **`Socio`**: relaciones `socioMembresia()`, `socioCuotas()`, `cuotasMembresia()`. Métodos `calcularPrincipalPorValor()` y `sincronizarMembresiaLegacy()`.
- **`SocioCuotaObserver`**: registrado en `AppServiceProvider`. Eventos `saved`/`deleted` invocan `sincronizarMembresiaLegacy()` como red de seguridad (solo actúa si no existe principal).

---

### FASE 3 — Módulo de Recepción, Acceso y Reportes

#### 3.1 Formulario de registro y edición ✅ COMPLETADA
- Dropdowns de estado por membresía en `SociosNuevo` y `SociosEditar`.
- Filtro `disponible=true` con excepción para membresías ya asignadas al socio.
- Validación: mínimo una membresía, sin duplicados.
- `comprobarMultiples()` deriva `claves_membresia` desde `estados_membresia`.
- Lógica de rotación de principal en `update()` con soporte para estado CAN.

#### 3.2 Pórtico de Acceso ✅ COMPLETADA
- Lista principal + adicionales con sus estados.
- Acceso denegado si `socioMembresia.estado === 'CAN'` y no hay adicionales.

#### 3.3 Interfaz de Estados de Cuenta ✅ COMPLETADA
- Badge "Tarifa especial" (morado) en `livewire/recepcion/estados/principal.blade.php` si el socio tiene `monto_personalizado` en `socios_cuotas`.
- Botón toggle "Tarifa especial" en barra de búsqueda para filtrar solo esos socios.
- **Limitación temporal:** badge no aparece para socios con una sola membresía hasta Fase 4.2.

#### 3.4 Reportes y exportaciones ✅ COMPLETADA
- `SociosExport`: `sumar_cuotas()` usa `monto_a_cobrar`. Nuevas columnas "MEMBRESIAS CONTRATADAS" (lista principal + adicionales) y "TARIFA PERSONALIZADA" (SÍ/NO).
- `CarteraVencidaExport`: nueva columna "MEMBRESIAS". Corregido crash por principal null. Filtro de cancelados ahora excluye solo si **todas** las membresías están canceladas (principal CAN y sin adicionales), vía helper `todasCanceladas()`.
- **Nota Option B:** tras Fase 4.2, la columna monetaria "MEMBRESIA" de `SociosExport` empezará a incluir la principal (al estar también en `socios_cuotas`); `listarMembresias()` ya deduplica con `unique()`.

---

### FASE 4 — Módulo de Sistemas y Facturación Masiva

#### 4.1 Modal "Editar Cuotas" en Sistemas ❌ Pendiente
- Botón "Editar Cuotas" por socio en `livewire/sistemas/recepcion/socios/lista-socios.blade.php`.
- Modal con listado de cuotas (membresías + cargos fijos) campo `monto_personalizado`.
- Validación contra duplicados antes de tocar la BD.

#### 4.2 Refactor de `CargosController::cargarMensualidades` + Opción B ❌ Pendiente

Este bloque agrupa tres cambios que deben implementarse juntos:

**A) Migración Opción B — principal en `socios_cuotas`**
- Nueva migración: para cada socio, insertar su membresía principal en `socios_cuotas` (backfill desde `socios_membresias`).
- `socios_membresias` queda como tabla de estado/referencia únicamente.
- `SocioForm.store()` y `update()`: además de escribir en `socios_membresias`, escribir la principal también en `socios_cuotas`.
- El badge de Fase 3.3 y `monto_personalizado` quedan disponibles para **todos** los socios.

**B) Refactor de `cargarMensualidades`**
- Eliminar la lógica de conteos agrupados y `reset()`.
- Nueva lógica: iterar únicamente `socios_cuotas` (ya incluye la principal):

```
Para cada socio activo (al menos una membresía no-CAN en socios_membresias):
    Para cada SocioCuota del socio:
        Si NO existe EstadoCuenta para (id_socio, id_cuota, mes, año):
            Crear EstadoCuenta con monto_a_cobrar
```

**C) Aplicación de `monto_a_cobrar` (Fase 4.3)**
- Al iterar `SocioCuota` directamente, se usa `$sc->monto_a_cobrar` — el accessor ya existe.
- No requiere trabajo adicional.

#### 4.3 Aplicación de `monto_a_cobrar` ❌ Pendiente
Se resuelve junto con Fase 4.2 al usar instancias `SocioCuota` con el accessor.

#### 4.4 Actualización masiva de cuotas base ✅ Sin cambios requeridos
`App/Livewire/Sistemas/Recepcion/Cuotas.php` ya modifica `cuotas.monto`. El cambio aplica automáticamente.

#### 4.5 Consumo mínimo con múltiples membresías ❌ Pendiente
- En `Anualidad/Nueva.php`: cambiar `Membresias::all()` a `Membresias::where('disponible', true)->get()`.
- Calcular el consumo mínimo como el **mayor** entre los `consumo_minimo` de todas las membresías activas del socio (estado distinto de CAN).

#### 4.6 Anualidad por membresía individual ❌ Pendiente
El código actual modifica `socios_membresias.estado = 'ANU'` globalmente. Refactorizar para distinguir:

- **Membresía principal** (`socios_membresias`): actualizar `estado = 'ANU'` — sin cambio de lógica.
- **Membresía adicional** (`socios_cuotas`): cambiar `id_cuota` a la cuota de tipo ANU de esa membresía. El estado queda implícito en `cuota->tipo`.

Requiere que el selector de membresía en el formulario de anualidad permita elegir entre principal y adicionales del socio.

---

## 3. VERIFICACIÓN Y PRUEBAS

### Prueba 1 — Múltiples membresías en Recepción
Registrar o editar socio con varias membresías. Verificar que `socios_membresias` apunta a la de mayor monto y las demás están en `socios_cuotas`.

### Prueba 2 — Cuotas personalizadas desde Sistemas (pendiente Fase 4.1)
Asignar precio especial a una membresía. Verificar en BD. Intentar duplicado → error amigable.

### Prueba 3 — Facturación con tarifa mixta (pendiente Fases 4.2/4.3)
Socio con `monto_personalizado` en una cuota. Ejecutar facturación y verificar que usa tarifa personalizada solo en esa cuota.

### Prueba 4 — Prevención de cobros duplicados (pendiente Fase 4.2)
Ejecutar `cargarMensualidades` dos veces en el mismo mes. Verificar cero duplicados en `estado_cuenta`.

### Prueba 5 — Pórtico con distintos estados
- Socio MEN/ANU → ACCESO PERMITIDO
- Socio INA → ACCESO PERMITIDO
- Socio CAN con adicionales → ACCESO PERMITIDO
- Socio CAN sin adicionales → ACCESO DENEGADO
- Socio sin membresía → ACCESO DENEGADO

### Prueba 6 — Anualidad individual (pendiente Fase 4.6)
Socio con dos membresías. Anualizar solo una. Verificar que la otra conserva su estado original.

---

## 4. ESTADO ACTUAL DE AVANCE

| Fase | Estado |
|---|---|
| Fase 1 — Migración estructural (monto_personalizado, FK, unique, disponible) | ✅ Completada |
| Fase 2 — Modelos (SocioCuota, Socio, Observer registrado) | ✅ Completada |
| Fase 3.1 — Dropdowns multi-membresía en Recepción | ✅ Completada |
| Fase 3.2 — Pórtico (acceso por estado, no solo existencia) | ✅ Completada |
| Fase 3.3 — Estados de Cuenta UI (badge + filtro tarifa especial) | ✅ Completada (limitación hasta Fase 4.2) |
| Fase 3.4 — Exportaciones (SociosExport, CarteraVencidaExport) | ✅ Completada |
| Fase 4.1 — Modal "Editar Cuotas" en Sistemas | ❌ Pendiente |
| Fase 4.2 — Refactor cargarMensualidades (reset bug + adicionales) | ❌ Pendiente |
| Fase 4.3 — Aplicación monto_a_cobrar (se resuelve con 4.2) | ❌ Pendiente |
| Fase 4.5 — Consumo mínimo agregado (mayor entre activas) | ❌ Pendiente |
| Fase 4.6 — Anualidad por membresía individual | ❌ Pendiente |

---

## 5. PLAN DE EMERGENCIA (ROLLBACK)

**Paso 1:** Revertir código a versión anterior estable mediante Git.

**Paso 2:** Las columnas `monto_personalizado` y `disponible` permanecen en BD pero son ignoradas por el código anterior. El club opera con tarifas base normales.

**Paso 3:** Si hay problema estructural, ejecutar `down()` de las migraciones correspondientes.
