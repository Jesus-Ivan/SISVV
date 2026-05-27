# Plan de Implementación Final: Sistema de Cuotas Personalizadas

**Proyecto:** Sistema de Cuotas Personalizadas para el Registro de Socios en Vista Verde Country Club.

**Fecha:** 2026-05-26

**Versión:** consolidada a partir de `implementation_plan.md` y `planDeImplementacion.md`, alineada con `requisitos.md`.

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

> **Nota:** Aunque RF 3 del documento `requisitos.md` menciona a Recepción, la regla de negocio acordada con el cliente es que la configuración de precios personalizados queda restringida a Sistemas, para garantizar control y trazabilidad sobre los descuentos otorgados.

---

## 1. ALINEACIÓN CON LOS REQUERIMIENTOS FUNCIONALES

### RF 1 — Múltiples membresías simultáneas
- Cada membresía contratada del socio se registra como una fila en la tabla `socios_cuotas`, vinculando al socio con la cuota correspondiente de tipo membresía (`MEN`).
- Un socio puede tener membresías de **distinto tipo** simultáneamente (ej. Campo de Golf Familiar `CG-FAM` + Casa Club Familiar `CC-FAM`). **No** puede tener dos veces exactamente la misma cuota (esto es lo que protege RF 2.6).
- La tabla `socios_membresias` se conserva como fuente histórica y de control de estado (`CAN`, `ANU`, `MEN`); mantiene **una sola fila por socio**, cuyo `clave_membresia` corresponde a la **membresía de mayor antigüedad** del socio (la más antigua según `created_at` en `socios_cuotas`). No hay UI para que el usuario seleccione una "principal" — el sistema la determina automáticamente.
- En el modelo `Socio` se mantiene la relación legacy `socioMembresia()` (HasOne) y se añaden:
  - `socioMembresias()` HasMany → `socios_membresias`
  - `socioCuotas()` HasMany → `socios_cuotas`
  - `cuotasMembresia()` HasMany filtrada por `cuota.tipo = 'MEN'`

### RF 2.1 — Mínimo una membresía obligatoria
Validación en `SocioForm` (regla `required` sobre el listado de membresías seleccionadas) que impida guardar un socio sin al menos una membresía.

### RF 2.2 — Cuotas base definidas
Las membresías y cargos fijos mantienen su precio base en la tabla `cuotas` (columna `monto`). Este precio aplica por defecto.

### RF 2.3 — Cuotas personalizadas ilimitadas
Se agrega la columna `monto_personalizado` (decimal, nullable) en `socios_cuotas`. Un socio puede tener precios especiales en cualquier cantidad de cuotas contratadas.

### RF 2.4 — Independencia entre socios
El precio personalizado se almacena únicamente en la fila individual de `socios_cuotas`. Modificarlo no altera el precio de ningún otro socio ni el catálogo base.

### RF 2.5 — Prevención de cobros duplicados por periodo
El proceso de facturación mensual itera de manera estricta por cada `SocioCuota` activa del socio. Para cada una, consulta si ya existe un registro en `EstadoCuenta` para el **mes y año** del proceso, **filtrando por `id_cuota` específico**:

```php
EstadoCuenta::where('id_socio', $socio->id)
    ->where('id_cuota', $cuota->id_cuota)
    ->whereYear('fecha', $fecha->year)
    ->whereMonth('fecha', $fecha->month)
    ->exists();
```

Si existe, el cargo se omite; si no, se factura. Esto resuelve el bug actual del `reset($cuotas_fijas)` y el conteo `count(fijas)-count(estado)` en `CargosController::cargarMensualidades`, que provocaba que al haber dos cuotas del mismo tipo (ej. dos lockers) ambas se facturaran con la misma fila reutilizada.

### RF 2.6 — Impedir membresías idénticas
- Se prohíbe contratar **dos veces exactamente la misma cuota** para un mismo socio (mismo `id_cuota`). Sí se permiten múltiples membresías de distinto tipo (ej. `CG-FAM` + `CC-FAM`).
- Índice único compuesto en `(id_socio, id_cuota)` de la tabla `socios_cuotas` (restricción a nivel de base de datos).
- Validación amigable en el modal de Sistemas que alerta al usuario antes de provocar una excepción SQL.

### Regla de membresía principal (uso interno legacy)
La tabla `socios_membresias` mantiene una sola fila por socio. El sistema determina automáticamente la **membresía principal** como aquella **de mayor antigüedad entre las que están activas** (estado distinto de `CAN`):

- Si el socio tiene al menos una membresía no cancelada → se elige la más antigua entre esas (por `created_at`, con desempate por `id` ascendente).
- Si todas sus membresías están canceladas → se elige la más antigua a secas (por `created_at`).

Esta decisión es **interna y no expuesta al usuario** — no existe UI para designar una principal manualmente. Su único propósito es servir de referencia para el código legacy que consulta `socio->socioMembresia`.

### Estado individual por membresía
Para soportar que un socio tenga una membresía cancelada y otra activa simultáneamente, se agrega la columna `estado` en `socios_cuotas` (valores `MEN`, `INA`, `ANU`, `CAN`). El estado deja de ser global del socio y pasa a ser por cuota individual.

### Regla de cancelación
Cancelar una membresía específica del socio **no elimina** la fila de `socios_cuotas` — solo cambia su `estado` a `CAN`. Esto preserva el histórico y permite reportar membresías pasadas.

### Regla de anualidad
La anualidad se gestiona **por membresía individual**. Si un socio paga anualidad sobre una de sus membresías, solo esa cuota pasa a estado `ANU`; las demás conservan su estado original. Esto permite combinaciones como FAMILIAR anualizada + INDIVIDUAL mensual.

### Regla de consumo mínimo con múltiples membresías
Cuando un socio tiene varias membresías activas (estado distinto de `CAN`), el **consumo mínimo aplicable** es el **mayor** entre los `consumo_minimo` de cada una. Ejemplo: si tiene `CG-FAM` ($5,000) y `CC-FAM` ($3,000), su consumo mínimo es de $5,000, no la suma ni el promedio.

### RF 3 — Configuración de Cuotas y Membresías
- **Recepción (`SociosNuevo`, `SociosEditar`):** se reemplaza el dropdown único de membresía por una lista de **checkboxes** que permite asignar/quitar múltiples membresías simultáneas. Se pueden activar/desactivar cargos fijos (lockers, resguardo) a tarifa base. **No se renderiza** el campo `monto_personalizado` en esta vista. **No** existe selector de "membresía principal" — el sistema la determina automáticamente por antigüedad.
- **Sistemas (`livewire/sistemas/recepcion/socios/lista-socios.blade.php`):** se agrega un botón "Editar Cuotas" por fila que abre un modal exclusivo donde se configuran:
  - Activar/desactivar membresías y cargos fijos del socio.
  - Capturar `monto_personalizado` por cada cuota (placeholder con el monto base del catálogo).
  - Validación amigable contra duplicados (RF 2.6).
- Este modal opera bajo el middleware `sistemas` para garantizar que sólo personal autorizado pueda asignar precios especiales.

### RF 4 — Registro en estado de cuenta
El proceso de facturación utilizará la propiedad `monto_a_cobrar` del modelo `SocioCuota`, que devuelve `monto_personalizado` si existe, o el `monto` base del catálogo si es `null`. Los cargos generados reflejarán esta lógica en el estado de cuenta.

### RF 5 — Reportes actualizados
- `SociosExport`: lista todas las membresías contratadas por cada socio. La función `sumar_cuotas` utilizará `monto_a_cobrar` en lugar de `cuota->monto`. Se añade columna **"TARIFA PERSONALIZADA"** con valor `SÍ` o `NO`.
- `CarteraVencidaExport`: lista todas las membresías contratadas separadas por comas. Se ajusta el filtro de cancelados para validar que el socio no tenga **todas** sus membresías canceladas antes de excluirlo.

### RF 6 — Visualización clara en Búsqueda y Escaneo (Pórtico)
- Pórtico de Acceso (`livewire/acceso/socios/principal.blade.php`): iterar y mostrar en lista todas las membresías del socio con su estado.
- El acceso al club se otorga si **al menos una** de las membresías del socio no está en estado `CAN`.

### RF 7 — Actualización masiva de cuotas base desde Sistemas
Implementado nativamente por el diseño. Al modificar el precio en la tabla `cuotas` desde `App/Livewire/Sistemas/Recepcion/Cuotas.php`, el cambio aplica automáticamente en el siguiente ciclo de facturación para los socios sin `monto_personalizado`. Los socios con precio personalizado conservan su tarifa especial intacta.

### RF 8 — Diferenciación visual de socios con tarifa especial
- **Estados de Cuenta (`livewire/recepcion/estados/principal.blade.php`):** badge junto al nombre del socio si tiene al menos una cuota con `monto_personalizado IS NOT NULL`. Filtro en la barra de búsqueda para listar solo socios con tarifa especial.
- **Reportes:** columna "TARIFA PERSONALIZADA" en `SociosExport`.

---

## 2. PLAN DE IMPLEMENTACIÓN POR FASES

### FASE 1 — Migración Estructural de Base de Datos ✅ COMPLETADA

#### 1.1 Alteración de `socios_cuotas`
- `id_socio` e `id_cuota` cambiados a `unsignedInteger` para homologar con `socios.id` y `cuotas.id`.
- Columna `monto_personalizado` decimal(10,2) nullable, posicionada después de `id_cuota`.
- Índice único compuesto `(id_socio, id_cuota)` (RF 2.6).
- Llaves foráneas: `id_socio → socios.id` (cascadeOnDelete), `id_cuota → cuotas.id` (nullOnDelete).
- `DELETE` previo de duplicados antes de aplicar el índice único.

#### 1.2 Corrección en migración de `cuotas`
- Ajustar el `down()` para que referencie `cuotas` en lugar de `cuotas_club`.

#### 1.3 Nueva migración: columna `estado` en `socios_cuotas` (pendiente)
- Agregar columna `estado VARCHAR(3) DEFAULT 'MEN'` en `socios_cuotas` (valores: `MEN`, `INA`, `ANU`, `CAN`).
- Backfill de datos legacy: copiar el estado actual desde `socios_membresias` a `socios_cuotas` para cada socio en el `up()` de la migración:
  ```sql
  UPDATE socios_cuotas sc
  INNER JOIN socios_membresias sm ON sc.id_socio = sm.id_socio
  INNER JOIN cuotas c ON sc.id_cuota = c.id
  SET sc.estado = sm.estado
  WHERE c.tipo = 'MEN';
  ```
- Las filas de `socios_cuotas` que correspondan a cargos fijos (locker, resguardo, etc.) quedan con el default `MEN`.

**Pendiente:** ejecutar `php artisan migrate` en entornos de prueba y producción tras respaldo.

---

### FASE 2 — Actualización de la Lógica Interna (Modelos) ✅ COMPLETADA

#### 2.1 Modelo `SocioCuota`
- `monto_personalizado` añadido a `$fillable`.
- Accessor `montoACobrar` (expuesto como `monto_a_cobrar`): devuelve `monto_personalizado ?? cuota->monto`.
- **Pendiente:** corregir la relación `cuota()` para que sea `belongsTo(Cuota::class, 'id_cuota')` en lugar de `hasOne` invertido.

#### 2.2 Modelo `Socio`
- Conservar `socioMembresia()` (HasOne) para compatibilidad.
- Agregar `socioMembresias()` HasMany, `socioCuotas()` HasMany, `cuotasMembresia()` HasMany filtrada por tipo `MEN`.

---

### FASE 3 — Módulo de Recepción, Acceso y Reportes

#### 3.1 Formulario de registro y edición de socios (Recepción)
- Reemplazar el dropdown único de membresía en `SociosNuevo` y `SociosEditar` por una **lista de checkboxes** de membresías disponibles.
- **No incluir UI** para seleccionar una "membresía principal" — la determinación es automática (la de mayor antigüedad).
- Implementar validación en `SocioForm`:
  - Al menos una membresía seleccionada (RF 2.1).
  - No permitir seleccionar dos veces exactamente la misma cuota (RF 2.6).
- Ajustar el método `comprobar()` para deshabilitar el registro de integrantes únicamente si **todas** las membresías seleccionadas son tipo "INDIVIDUAL".
- Sincronización con `socios_membresias` legacy:
  - Todas las membresías se registran en `socios_cuotas`.
  - La fila en `socios_membresias` se mantiene única por socio y refleja la **membresía de mayor antigüedad** (la más antigua según `created_at` en `socios_cuotas`).
  - Centralizar esta sincronización en un método único (`sincronizarMembresiaLegacy`) idealmente disparado por un Eloquent observer sobre `SocioCuota` (saved/deleted) para evitar inconsistencias.

#### 3.2 Pórtico de Acceso (RF 6)
- Modificar `livewire/acceso/socios/principal.blade.php` y su controlador para listar todas las membresías del socio (vía relación `cuotasMembresia` o `socioMembresias`).
- Permitir acceso si **al menos una** membresía no está en estado `CAN`.

#### 3.3 Interfaz de Estados de Cuenta (RF 8)
- Modificar `livewire/recepcion/estados/principal.blade.php` y `Estados/Principal.php`.
- Mostrar badge junto al nombre del socio si tiene al menos una `SocioCuota` con `monto_personalizado IS NOT NULL`.
- Integrar filtro toggle/select en la barra de búsqueda para listar solo socios con tarifa especial.

#### 3.4 Reportes y exportaciones
- `SociosExport`: usar `monto_a_cobrar` en `sumar_cuotas`. Añadir columna "TARIFA PERSONALIZADA" (SÍ/NO).
- `CarteraVencidaExport`: listar todas las membresías por socio separadas por comas. Filtrar cancelados validando que **todas** las membresías del socio estén canceladas.

---

### FASE 4 — Módulo de Sistemas y Facturación Masiva

#### 4.1 Modal "Editar Cuotas" en Sistemas (RF 3 — Sistemas exclusivo)
- En `livewire/sistemas/recepcion/socios/lista-socios.blade.php`, agregar botón "Editar Cuotas" por fila bajo middleware `sistemas`.
- Crear modal Livewire dedicado con:
  - Listado de todas las cuotas disponibles (membresías + cargos fijos).
  - Checkbox por cuota para activarla/desactivarla en `socios_cuotas`.
  - Campo numérico contiguo para `monto_personalizado` (placeholder = monto base del catálogo).
- Validación amigable que impida seleccionar dos veces la misma membresía o cuota antes de tocar la BD.

#### 4.2 Refactor de `CargosController::cargarMensualidades` (RF 2.5)
- Eliminar la lógica actual de conteos agrupados (`count(fijas)-count(estado)`) y la reutilización del primer elemento con `reset()`.
- Nueva lógica:
  ```
  Para cada socio activo (al menos una membresía no cancelada):
      Para cada SocioCuota del socio:
          Si NO existe EstadoCuenta para (id_socio, id_cuota, mes, año):
              Crear EstadoCuenta usando $socioCuota->monto_a_cobrar
  ```
- Esto resuelve el bug de cobros duplicados al haber múltiples cuotas del mismo tipo y garantiza que cada cuota individual se verifique de forma independiente.

#### 4.3 Aplicación de precios personalizados
- Toda la facturación masiva (mensualidades, cargos fijos, anualidades) debe usar `$socioCuota->monto_a_cobrar` en lugar de leer directamente `cuota->monto`.

#### 4.4 Actualización masiva de cuotas base (RF 7)
- No requiere cambios: `App/Livewire/Sistemas/Recepcion/Cuotas.php` ya modifica la tabla `cuotas`. El cambio se aplica automáticamente a socios sin precio personalizado en el siguiente ciclo de facturación.

#### 4.5 Cálculo de consumo mínimo con múltiples membresías
- Refactorizar la lógica de anualidades / consumo mínimo en `App/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php` (y donde se calcule el consumo mínimo) para que tome el **mayor `consumo_minimo`** entre todas las membresías activas del socio (estado distinto de `CAN`).
- Si el socio tiene una única membresía, el comportamiento queda idéntico al actual.
- Si tiene varias, se ignoran las canceladas y se aplica el `consumo_minimo` más alto.

#### 4.6 Refactor de anualidad por membresía individual
- Hoy `CargosController:259` y `:283` modifican `socios_membresias.estado` para marcar/restaurar anualidad. Refactorizar para que opere sobre `socios_cuotas.estado` de la cuota específica que se está anualizando.
- Cada membresía se anualiza por separado. El estado `ANU` solo se asigna a la cuota seleccionada.
- Al expirar la anualidad, se restaura el estado anterior (`estado_mem_f`) solo en esa cuota.

#### 4.7 Cancelación de membresía individual (sin pérdida de histórico)
- Al desmarcar una cuota desde la UI de Recepción (o vía edición de Sistemas), no se elimina la fila de `socios_cuotas` — se cambia su `estado` a `CAN`.
- Esto preserva el histórico para reportes y permite reactivar la membresía en el futuro si el socio decide volver.
- Solo cuando un socio se elimina por completo (acción explícita), se eliminan sus filas en cascada vía la FK `id_socio → socios.id`.

#### 4.8 Sincronización automática de la fila legacy `socios_membresias`
- Crear método `Socio::sincronizarMembresiaLegacy()` que recalcula la fila de `socios_membresias` siguiendo la regla "más antigua activa" (con fallback a la más antigua a secas si todas están canceladas).
- Disparar este método mediante un Eloquent observer en `SocioCuota` (eventos `saved` y `deleted`) para evitar que el código tenga que recordarlo manualmente.

---

## 3. VERIFICACIÓN Y PRUEBAS

### Prueba 1 — Asignación de múltiples membresías en Recepción
Crear o editar un socio desde Recepción marcando múltiples casillas de membresía. Verificar que todas se guarden en `socios_cuotas` sin errores y que `socios_membresias` mantenga la fila de referencia.

### Prueba 2 — Configuración de cuotas personalizadas desde Sistemas
- Ingresar a Sistemas → Recepción → Lista de Socios → botón "Editar Cuotas".
- Asignar un precio especial a una membresía y activar la cuota de Locker con su precio base.
- Verificar que la BD se actualice correctamente.
- Intentar asignar la misma cuota dos veces y verificar que aparezca un error de validación amigable (no excepción SQL).

### Prueba 3 — Actualización masiva de tarifa base y cobro mixto (RF 7)
- Modificar el precio de una cuota base desde el catálogo de Sistemas.
- Ejecutar la facturación mensual para el socio con precio personalizado de la Prueba 2.
- Verificar que los socios con tarifa base reciben el nuevo precio, mientras que el socio con `monto_personalizado` conserva su tarifa especial.

### Prueba 4 — Prevención de cobros duplicados con múltiples cuotas del mismo tipo
- Asignar al socio dos cuotas del mismo tipo (ej. dos lockers, ambos activos).
- Generar manualmente un cargo en `EstadoCuenta` para uno de los lockers en el mes corriente.
- Ejecutar la facturación masiva.
- Validar:
  - La cuota que ya tenía cargo no se factura nuevamente (cero duplicados).
  - La segunda cuota sí se factura.
  - Los saldos pendientes del mes anterior permanecen intactos.

### Prueba 5 — Restricción de membresías idénticas (RF 2.6)
Intentar asignar la misma membresía dos veces al mismo socio (desde Recepción y desde el modal de Sistemas). Verificar que ambos contextos muestren un error de validación amigable y que la BD rechace la operación por el índice único.

### Prueba 6 — Visualización en Pórtico (RF 6)
Escanear el número de socio en el Pórtico. Confirmar que se listan **todas** las membresías contratadas con su respectivo estado. Verificar que el acceso se otorgue mientras al menos una membresía esté activa.

### Prueba 7 — Identificación visual en Estados de Cuenta (RF 8)
- Acceder al listado de Estados de Cuenta en Recepción y activar el filtro "Solo con Tarifa Especial".
- Verificar que solo se listen socios con `monto_personalizado` y que aparezca el badge.
- Exportar a Excel y validar que la columna "TARIFA PERSONALIZADA" registre `SÍ`/`NO` correctamente y que la suma de cuotas refleje los precios personalizados.

---

## 4. PLAN DE EMERGENCIA (ROLLBACK)

En caso de detectarse una falla crítica en producción:

**Paso 1:** Revertir el código del sistema a la versión anterior estable mediante Git.

**Paso 2:** La columna `monto_personalizado` y el índice único en `socios_cuotas` permanecerán en la base de datos pero serán ignorados por el código de la versión anterior. El club continúa operando con las tarifas base normales sin interrupción.

**Paso 3:** Si el problema es estructural (FK o índice rompiendo inserts), ejecutar el `down()` de la migración `2026_05_22_142722_alter_socios_cuotas_add_monto_personalizado.php` para revertir a la estructura original.

---

## 5. ESTADO ACTUAL DE AVANCE

| Fase | Estado |
|---|---|
| Fase 1.1/1.2 — Migración inicial (`monto_personalizado`, FK, unique, `cuotas.down()`) | ✅ Código listo, pendiente ejecutar `php artisan migrate` |
| Fase 1.3 — Nueva migración: columna `estado` en `socios_cuotas` | ❌ Pendiente |
| Fase 2 — Modelos | ✅ Completada (pendiente correción menor: `cuota()` a `belongsTo`) |
| Fase 3.1 — Checkboxes multi-membresía en Recepción | 🟡 Sólo validación `required` implementada; falta UI checkboxes |
| Fase 3.2 — Pórtico | ❌ Pendiente |
| Fase 3.3 — Estados de Cuenta UI | ❌ Pendiente |
| Fase 3.4 — Exports | ❌ Pendiente |
| Fase 4.1 — Modal "Editar Cuotas" en Sistemas | ❌ Pendiente |
| Fase 4.2 — Refactor `cargarMensualidades` | ❌ Pendiente |
| Fase 4.3 — Aplicación de `monto_a_cobrar` | ❌ Pendiente |
| Fase 4.5 — Consumo mínimo agregado (mayor) | ❌ Pendiente |
| Fase 4.6 — Anualidad por membresía individual | ❌ Pendiente |
| Fase 4.7 — Cancelación individual sin pérdida de histórico | ❌ Pendiente |
| Fase 4.8 — Sincronización automática de fila legacy (observer) | ❌ Pendiente |


