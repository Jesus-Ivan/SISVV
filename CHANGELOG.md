# CHANGELOG — Sistema de Cuotas Personalizadas

Registro de cambios del proyecto de mejora: **Sistema de Cuotas Personalizadas para el Registro de Socios en Vista Verde Country Club.**

---

## Fase 1 — Migración Estructural de Base de Datos
**Fecha:** 2026-05-22

### Archivos modificados

#### `database/migrations/2026_05_22_142722_alter_socios_cuotas_add_monto_personalizado.php`
Migración existente que estaba incompleta. Se completó con los siguientes cambios:

- `id_socio` cambiado de `integer` a `unsignedInteger` para homologar con `socios.id`
- `id_cuota` cambiado de `integer nullable` a `unsignedInteger nullable` para homologar con `cuotas.id`
- Columna `monto_personalizado` agregada: tipo `decimal(10,2)`, nullable, posicionada después de `id_cuota`
- Llave foránea `id_socio → socios.id` con `cascadeOnDelete`
- Llave foránea `id_cuota → cuotas.id` con `nullOnDelete`
- Índice único compuesto `(id_socio, id_cuota)` — ya existía, se conservó

#### `database/migrations/2024_03_23_190756_create_cuotas_table.php`
- Corregido bug en `down()`: referenciaba `cuotas_club` en lugar de `cuotas`

### Estado
- [x] Migración ejecutada en local — verificada en BD

---

## Fase 2 — Actualización de la Lógica Interna (Modelos de Laravel)
**Fecha:** 2026-05-22

### Archivos modificados

#### `app/Models/SocioCuota.php`
- Reemplazado `$guarded = ['id']` por `$fillable` explícito incluyendo `monto_personalizado`
- Agregado accessor `montoACobrar` (expuesto como `$socioCuota->monto_a_cobrar`): devuelve `monto_personalizado` si existe, o el `monto` base del catálogo si es null
- Eliminados imports no utilizados (`HasMany`)

#### `app/Models/Socio.php`
- Conservada relación `socioMembresia()` (HasOne → `socios_membresias`) para compatibilidad con código existente
- Agregada relación `socioMembresias()` (HasMany → `socios_membresias`): todas las membresías del socio
- Agregada relación `socioCuotas()` (HasMany → `socios_cuotas`): todas las cuotas asignadas al socio
- Agregada relación `cuotasMembresia()` (HasMany → `socios_cuotas` filtrado por `tipo = 'MEN'`): solo las cuotas de tipo membresía

---

## Fase 3.1.a — Validación de membresía obligatoria en formulario de socios
**Fecha:** 2026-05-26

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- Agregada regla `required` a `clave_membresia` en `$socio_rules`, aplicando la validación tanto en `store()` como en `update()`

#### `resources/views/livewire/recepcion/socios-editar.blade.php`
- Corregido `@error('formSocio.clave_membresia')` → `@error('form.clave_membresia')`: necesario para que el mensaje de validación de membresía obligatoria sea visible en la vista de edición

#### `app/Livewire/Recepcion/SociosEditar.php`
- Agregada guarda en `saveSocio()`: si `clave_membresia` está vacío, delega directo a `actualizarSocio()` para que la validación del formulario muestre el error. Necesario para evitar el crash en `revisarCambioMembresia()` cuando `Membresias::find(null)` retorna null y se intenta leer `->descripcion`

### Mensaje de error personalizado
- En `SocioForm` se agregó `$messages` con el texto: `"Selecciona al menos una membresía."` para `clave_membresia.required`

---

## Fase 1.3 — Migración: columna `estado` en `socios_cuotas`
**Fecha:** 2026-05-26

### Archivos creados

#### `database/migrations/2026_05_26_191442_alter_socios_cuotas_add_estado.php`
- Agrega columna `estado VARCHAR(3) DEFAULT 'MEN'` a `socios_cuotas`, posicionada después de `monto_personalizado`
- Backfill: copia el estado actual desde `socios_membresias` hacia las cuotas de tipo `MEN` para preservar el estado de los socios existentes
- Las cuotas de cargos fijos (locker, resguardo, etc.) quedan con el default `MEN`
- `down()` simple: elimina la columna

### Archivos modificados

#### `app/Models/SocioCuota.php`
- Agregado `estado` al array `$fillable`

### Estado
- [x] Migración ejecutada en local — verificada en BD
- Permite estados independientes por membresía del socio (ej: FAMILIAR `CAN` + INDIVIDUAL `MEN`)

---

## Fase 4.8 — Sincronización automática de la fila legacy `socios_membresias`
**Fecha:** 2026-05-26

### Archivos creados

#### `app/Observers/SocioCuotaObserver.php`
- Observer Eloquent que escucha eventos `saved` y `deleted` sobre `SocioCuota`
- En ambos eventos invoca `Socio::sincronizarMembresiaLegacy()` para mantener actualizada la fila de `socios_membresias`
- Centraliza la lógica → ningún controlador o componente Livewire necesita recordar la sincronización

### Archivos modificados

#### `app/Models/Socio.php`
- Agregado método `calcularPrincipalPorAntiguedad(): ?SocioCuota`
  - Devuelve la cuota MEN más antigua entre las no canceladas
  - Fallback: si todas están canceladas, la más antigua a secas
  - Desempate por `created_at` ascendente y luego `id` ascendente
- Agregado método `sincronizarMembresiaLegacy(): void`
  - Calcula la principal por antigüedad y hace `updateOrCreate` en `socios_membresias`
  - Si el socio queda sin membresías, elimina la fila legacy

#### `app/Providers/AppServiceProvider.php`
- Registrado `SocioCuotaObserver` sobre `SocioCuota` en el método `boot()`

### Pruebas realizadas en local
- Cambio de estado en `socios_cuotas` (MEN → INA) → fila legacy se actualizó automáticamente a INA
- Restauración (INA → MEN) → fila legacy regresó a MEN
- Sin necesidad de invocar manualmente la sincronización en ningún punto del código

---

## Fase 3.1.b — Checkboxes multi-membresía en registro de socio (Recepción)
**Fecha:** 2026-05-26

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- Agregada propiedad `$claves_membresia = []` (array) para registro multi-membresía. Se conserva `$clave_membresia` (single) para el flujo de edición que aún no migra
- Agregadas reglas de validación al `$messages`: `claves_membresia.required`, `array`, `min`, `*.distinct` (RF 2.1 / RF 2.6)
- Reescrito el método `store()`:
  - Valida `claves_membresia` como array requerido con elementos únicos
  - Crea una fila en `socios_cuotas` por cada membresía seleccionada (con `estado=MEN`, `auto_delete=true`)
  - **Eliminada** la llamada manual a `SocioMembresia::create()` — el `SocioCuotaObserver` (Fase 4.8) se encarga de mantener sincronizada la fila legacy automáticamente
- Agregado método `comprobarMultiples()`: evalúa el array de membresías y solo deshabilita el registro de integrantes si **todas** son tipo INDIVIDUAL (Fase 3.1 del plan)

#### `app/Livewire/Recepcion/SociosNuevo.php`
- Renombrado `comprobarMembresia($value)` → `comprobarMembresias()` (sin argumento, lee directamente el array del form)
- Invoca `formSocio->comprobarMultiples()` en lugar del método legacy

#### `resources/views/livewire/recepcion/socios-nuevo.blade.php`
- Reemplazado el dropdown único de membresía por una lista de **checkboxes** dentro de un contenedor scrollable
- Cada checkbox: `wire:model="formSocio.claves_membresia"` + `wire:change="comprobarMembresias"`
- Agregados los `@error('formSocio.claves_membresia')` y `@error('formSocio.claves_membresia.*')` para mostrar errores del array y de elementos individuales

### Cobertura
- ✅ RF 1: múltiples membresías simultáneas (ej: CG-F + CC-F)
- ✅ RF 2.1: validación de al menos una membresía
- ✅ RF 2.6: validación amigable contra duplicados (regla `distinct`)
- ✅ No hay UI para "membresía principal" — sincronización automática vía observer por antigüedad
- ⏳ Flujo de **edición** (`SociosEditar`) aún usa el dropdown legacy (Fase 3.1.c)

---

## Fase 3.1.c — Checkboxes multi-membresía en edición de socio (Recepción)
**Fecha:** 2026-05-27

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- Agregada propiedad `$estados_membresia` (mapa `clave_membresia => estado`) para que la edición pueda configurar el estado individual de cada membresía
- `setSocio()` ahora carga `$claves_membresia` y `$estados_membresia` desde las cuotas MEN no canceladas del socio. Las canceladas (`estado=CAN`) quedan ocultas como historial
- Reescrito `update()`:
  - Validación nueva: `claves_membresia` requerido como array con elementos únicos; `estados_membresia.*` debe ser `MEN`, `INA` o `ANU`
  - **Diff de membresías**: por cada checkbox marcado se hace `updateOrCreate` apuntando al `id_cuota` que corresponde al estado deseado (MEN/INA/ANU comparten clave pero usan filas distintas en `cuotas`)
  - **Cancelación con preservación de historial**: las membresías que dejan de estar marcadas se actualizan a `estado=CAN` sin eliminarse
  - **Reactivación**: si una cuota previamente CAN se vuelve a marcar, `updateOrCreate` la regresa al estado seleccionado
  - Eliminada la escritura manual a `SocioMembresia` — el `SocioCuotaObserver` (Fase 4.8) la mantiene sincronizada

#### `app/Livewire/Recepcion/SociosEditar.php`
- Renombrado `comprobarMembresia($value)` → `comprobarMembresias()` (sin argumento — lee directo el array del form)
- Reescrito `saveSocio()`: valida primero que haya al menos una membresía seleccionada; si no, delega a `actualizarSocio()` para que aparezca el error de validación
- Reemplazado `revisarCambioMembresia()` por `revisarPerdidaIntegrantes()`: dispara el modal de advertencia solo si **todas** las membresías seleccionadas son INDIVIDUAL **y** hay integrantes registrados

#### `resources/views/livewire/recepcion/socios-editar.blade.php`
- Eliminado el dropdown de "Estado de membresía" (legacy, ya no representa el estado real con multi-membresía)
- Reemplazado el dropdown de "Membresía" por una lista de checkboxes con dropdown de estado por fila (Activa/Inactiva/Anual). El dropdown de estado se deshabilita automáticamente si el checkbox no está marcado
- Actualizado el mensaje del modal de advertencia: ahora indica "Pérdida de integrantes" cuando todas las seleccionadas son INDIVIDUAL

### Verificación en local
- El socio 7828 (creado en fase anterior con CC-I + CG-I) carga correctamente en la vista de edición: ambos checkboxes pre-marcados, ambos dropdowns en "Activa"
- El observer mantiene sincronizada la fila legacy en cada cambio de estado

### Ajustes de diseño (post-feedback)
- **Eliminada opción "Anual"** del dropdown de estado (igual que sistema original — anualidad solo se gestiona desde `CargosController`). Sigue mostrándose en solo lectura cuando la membresía ya está en ANU
- **Membresía ANU = solo lectura**: checkbox deshabilitado + dropdown deshabilitado + badge "ANUAL" amarillo junto al nombre. Evita cancelar accidentalmente una anualidad pagada
- **Rediseño visual**: lista con divisores entre filas, padding mayor, hover state, dropdown legible con etiqueta "Estado" y ancho mínimo
- **Texto guía**: nota debajo de la lista explicando que desmarcar = cancelar y que las anualidades se gestionan en otro módulo

### Corrección de `update()`
- La lógica anterior con `updateOrCreate(['id_socio', 'id_cuota'])` podía crear duplicados al cambiar de estado (creaba una nueva fila por cada tipo). Corregido para que busque por `clave_membresia` y actualice in-place el `id_cuota` y `estado` cuando ya existe la fila — respeta el invariante histórico de "una fila por clave_membresia por socio"

---

## Backfill de datos legacy en `socios_cuotas`
**Fecha:** 2026-05-27

### Archivos creados

#### `database/migrations/2026_05_27_135028_backfill_socios_cuotas_desde_legacy.php`
Migración correctiva de datos históricos:
- **Paso 1**: Corrige `socios_cuotas.estado` para 10 filas donde la cuota es tipo `INA`/`ANU` pero el estado había quedado en `MEN` (el backfill original solo sincronizaba tipo `MEN`)
- **Paso 2**: Crea filas en `socios_cuotas` para socios legacy activos con cuota exacta en catálogo (28 socios: 27 ANU + 1 MEN)
- **Paso 3**: Crea filas con fallback a tipo MEN para socios con `clave_membresia` que no tiene cuota del tipo requerido en catálogo

### Resultado en BD
- Filas con `estado=ANU` en `socios_cuotas`: 3 → 30
- Inconsistencias estado/tipo: 11 → 1 (caso especial `tipo=EST`, no aplica)
- Socios sin `socios_cuotas`: 45 → 17 (todos con claves `INT`/`COR` que no existen en catálogo)

### Impacto en operación
- **No modifica** `estado_cuenta`, recibos, cargos históricos ni cobros previos
- **No altera** la lógica de facturación actual (`CargosController` sigue leyendo `socios_membresias` hasta Fase 4.2)
- Permite que estos socios aparezcan correctamente en la nueva UI de edición de Recepción

---

## Fix — `cuotasMembresia()` no incluía membresías en estado ANU/INA
**Fecha:** 2026-05-27

### Problema detectado
La relación `Socio::cuotasMembresia()` filtraba por `cuota.tipo = 'MEN'`, lo que excluía las cuotas apuntando a tipos ANU o INA del catálogo (patrón legacy donde `id_cuota` se intercambia según el estado). Resultado: los socios con membresía anualizada no aparecían pre-marcados en la vista de edición.

### Archivos modificados

#### `app/Models/Socio.php`
- Cambiado el filtro de `cuotasMembresia()` de `where('tipo', 'MEN')` a `whereNotNull('clave_membresia')`. Ahora la relación devuelve cualquier `SocioCuota` cuya cuota represente una membresía (sin importar si está en estado MEN, INA o ANU), excluyendo solo los cargos fijos (locker, resguardo, etc.) que no tienen `clave_membresia`
