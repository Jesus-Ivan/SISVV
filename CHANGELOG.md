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

---

## Pivote arquitectónico — `socios_cuotas` sin columna `estado`
**Fecha:** 2026-05-28

### Contexto
Se replanteó la arquitectura de estados. La columna `estado` agregada en la Fase 1.3 se **revierte**: las membresías adicionales ya no guardan estado propio, sino que lo **derivan del `cuota->tipo`** (MEN/INA/ANU) según a qué cuota del catálogo apunta `id_cuota`. El único estado oficial vive en `socios_membresias.estado`.

### Archivos creados

#### `database/migrations/2026_05_28_152646_drop_estado_from_socios_cuotas.php`
- `up()`: elimina la columna `estado` de `socios_cuotas`
- `down()`: restaura `estado VARCHAR(3) DEFAULT 'MEN'`

### Archivos modificados

#### `app/Models/SocioCuota.php`
- Eliminado `estado` del array `$fillable`

#### `app/Models/Socio.php`
- **Renombrado** `calcularPrincipalPorAntiguedad()` → `calcularPrincipalPorValor()`: la membresía principal ahora se determina por **mayor `monto` base** (no por antigüedad). Devuelve la membresía adicional de mayor monto, usada al rotar la principal

### Nueva definición de "principal"
- **Principal = membresía de mayor `monto` base** del catálogo (cuota tipo MEN). Desempate: `created_at` asc, luego `id` asc
- Sigue siendo automática e interna — sin UI para designarla

---

## Corrección crítica — Observer desconectado y método fantasma
**Fecha:** 2026-05-28

### Problema detectado
- `AppServiceProvider::boot()` estaba **vacío**: el `SocioCuotaObserver` nunca se registraba, por lo que la sincronización automática de `socios_membresias` no ocurría
- El observer invocaba `$socio->sincronizarMembresiaLegacy()`, método que **no existía** en `Socio` — habría lanzado `BadMethodCallException` si el observer hubiera estado activo

### Archivos modificados

#### `app/Providers/AppServiceProvider.php`
- Registrado `SocioCuota::observe(SocioCuotaObserver::class)` en `boot()`

#### `app/Models/Socio.php`
- Implementado `sincronizarMembresiaLegacy()` como **red de seguridad**: solo actúa cuando el socio no tiene fila en `socios_membresias` (promueve la membresía adicional de mayor monto a principal). Las rotaciones completas las gestiona `SocioForm`
- Agregada bandera estática `$sincronizando` para evitar reentrada del observer durante el `delete()` interno

---

## Fase 3.1 (rediseño) — Dropdowns de estado y soporte de cancelación (CAN)
**Fecha:** 2026-05-28

### Contexto
Se reemplazan los checkboxes por **dropdowns de estado por membresía**. El mapa `estados_membresia` (clave → estado) pasa a ser la **fuente de verdad**; `claves_membresia` se deriva de él.

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- `comprobarMultiples()` reescrito: deriva `claves_membresia` filtrando `estados_membresia` por valores no vacíos. Solo las claves activas (no CAN) cuentan para bloquear el registro de integrantes
- `update()` reescrito con soporte completo de estado **CAN**:
  - Validación: `estados_membresia.*` ahora acepta `MEN,INA,ANU,CAN`
  - Ordena candidatos por monto descendente; separa activos (no CAN) de cancelados
  - **Cancelación total**: si todas las membresías quedan CAN, conserva la de mayor monto como CAN en `socios_membresias` y elimina todas de `socios_cuotas`
  - **Rotación de principal**: la antigua principal baja a `socios_cuotas` y la nueva sube a `socios_membresias`
  - **Fix duplicados**: variable `$claveMovidaAdicional` evita reinsertar en `socios_cuotas` la principal recién rotada (corregía SQLSTATE 23000 duplicate key `(id_socio, id_cuota)`)

#### `app/Livewire/Recepcion/SociosEditar.php`
- `revisarPerdidaIntegrantes()`: las membresías marcadas CAN se ignoran (se eliminarán) al decidir si se perderían los integrantes

#### `resources/views/livewire/recepcion/socios-editar.blade.php`
- Lista de todas las membresías del catálogo con **dropdown de estado** por fila (Seleccionar / Activa / Inactiva / Cancelada). Badge "Principal" y badge "ANUAL" según corresponda

---

## Columna `disponible` en el catálogo de membresías
**Fecha:** 2026-05-28

### Archivos creados

#### `database/migrations/2026_05_28_172345_add_disponible_to_membresias.php`
- Agrega `disponible` (boolean, default `true`) a `membresias`
- Marca como `false`: `CC-V-C`, `CC-V-S`, `CG-V-C`, `CG-V-S`, `COR`, `CUR`, `EST`, `EVE`, `INT`

### Archivos modificados

#### `app/Livewire/Recepcion/SociosNuevo.php`
- `membresias()` filtra por `disponible = true`

#### `app/Livewire/Recepcion/SociosEditar.php`
- `membresias()` muestra las disponibles **más** las que el socio ya tiene asignadas (`orWhereIn('clave', $this->form->claves_membresia)`), para no ocultar membresías legacy (INT, viudas, etc.) en edición

### Propósito
- `disponible` controla qué membresías aparecen en los formularios de registro/edición. No afecta facturación, acceso ni reportes (operan sobre datos ya persistidos)

---

## Fase 3.2 — Pórtico de Acceso por estado real
**Fecha:** 2026-05-28

### Archivos modificados

#### `resources/views/livewire/acceso/socios/principal.blade.php`
- La condición de acceso ya no se basa solo en la **existencia** de membresía. Ahora:
  - **ACCESO PERMITIDO** si la principal existe y `estado !== 'CAN'`, o si hay al menos una membresía adicional
  - **ACCESO DENEGADO** si la única membresía es CAN, o si no hay membresías

---

## Fase 3.3 — Estados de Cuenta: badge de tarifa especial y filtro
**Fecha:** 2026-05-28

### Archivos modificados

#### `app/Livewire/Recepcion/Estados/Principal.php`
- Nueva propiedad `soloTarifaEspecial` (bool)
- `resultSocios()` usa `withExists` para exponer `tiene_tarifa_especial` (socio con alguna `socios_cuotas.monto_personalizado IS NOT NULL`) y aplica `whereHas` cuando el filtro está activo
- Métodos `toggleTarifaEspecial()` y `setConceptos()` que llaman `resetPage()`; `updated()` también resetea la paginación al cambiar cualquier filtro
- Eliminado `limit(30)` muerto que `paginate()` ignoraba

#### `resources/views/livewire/recepcion/estados/principal.blade.php`
- Badge morado "Tarifa especial" junto al nombre del socio cuando `tiene_tarifa_especial`
- Botón toggle "Tarifa especial" en la barra de búsqueda
- **Rediseño de la sección de filtros**: dos filas limpias (búsqueda + toggle arriba; fechas, pills de Conceptos y select de Vista abajo). Los radio buttons se reemplazan por pills compactos

---

## Fase 3.4 — Reportes y exportaciones
**Fecha:** 2026-05-28

### Archivos modificados

#### `app/Exports/SociosExport.php`
- `sumar_cuotas()` usa `monto_a_cobrar` en lugar de `cuota->monto` (refleja tarifas personalizadas)
- Nueva columna **"MEMBRESIAS CONTRATADAS"**: lista principal + adicionales separadas por comas (helper `listarMembresias()`, deduplicado con `unique()`)
- Nueva columna **"TARIFA PERSONALIZADA"** (SÍ/NO)

#### `app/Exports/CarteraVencidaExport.php`
- Carga `cuotasMembresia.cuota` además de `socioMembresia`
- **Bug corregido**: ya no crashea cuando el socio no tiene fila en `socios_membresias` (acceso a índice null)
- Filtro de cancelados reescrito (helper `todasCanceladas()`): excluye un socio solo si la principal es CAN **y** no tiene membresías adicionales activas
- Nueva columna **"MEMBRESIAS"** con todas las membresías del socio

### Verificación en local
- `SociosExport`: 275 filas, nuevas columnas pobladas correctamente
- `CarteraVencidaExport`: ejecutado con/sin cancelados sin errores; el filtro excluye exactamente los socios con todas sus membresías canceladas
