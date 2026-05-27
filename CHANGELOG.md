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
