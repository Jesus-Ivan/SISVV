# CHANGELOG — Sistema de Cuotas Personalizadas

Registro de cambios del proyecto de mejora: **Sistema de Cuotas Personalizadas para el Registro de Socios en Vista Verde Country Club.**

---

## Tabla de estado de cuenta con borrado y deshacer en "Cargar Anualidad" (Sistemas)
**Fecha:** 2026-06-17

### Contexto
En `sistemas/recepcion/cargo-anualidades`, al seleccionar un socio se muestra una tabla con los
movimientos de su estado de cuenta, pensada para **borrar cuotas de membresía pendientes** (p. ej. un
socio que debe varios meses) como parte del alta de la anualidad. El borrado es **real e inmediato**
(no diferido como la tabla de cargos fijos), con un botón **"Deshacer"** que reinserta lo borrado.

### Archivos modificados

#### `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php`
- Computed `estadoCuentaSocio()`: movimientos de `estados_cuenta` del socio, más recientes primero,
  filtrados a `cargo > 0` **y** `id_cuota` no null (solo cuotas reales como membresías/recargos;
  excluye consumos de venta, notas y abonos que no tienen `id_cuota`)
- Propiedad `#[Locked] estadoCuentaBorrados`: copias completas de las filas borradas para poder deshacer
- `borrarEstadoCuenta($id)`: borrado real e inmediato acotado al socio (`id` + `id_socio`), guarda una
  copia antes de borrar. **Sin** confirmación ni notificación
- `restaurarEstadoCuenta()`: reinserta las copias con `EstadoCuenta::insert()` **conservando el `id`
  original**, lo que mantiene íntegras las referencias (`detalles_recibo.id_estado_cuenta`,
  `id_venta_pago`) si se deshace
- `selectedSocio()`: limpia el historial de deshacer al cambiar de socio

#### `resources/views/livewire/sistemas/recepcion/anualidad/nueva.blade.php`
- Nueva tabla "Estado de cuenta del socio" en la columna derecha (antes reservada), con scroll y
  encabezado `sticky`; columnas Fecha · Concepto · Cargo · Saldo · Acciones
- Botón papelera por fila (borrado inmediato, sin diálogo de confirmación)
- Banner ámbar "Se eliminaron N concepto(s) del estado de cuenta" con botón **"Deshacer"**

### Consideración de impacto entre módulos
- `estados_cuenta` **no tiene FK reales ni SoftDeletes** (las FK están comentadas en las migraciones).
  Otras tablas referencian su `id` vía INNER JOIN: `detalles_recibo.id_estado_cuenta`
  (`RecibosExport`, `ReportesController`) y los consumos por `id_venta_pago` (`VentaEditarForm`)
- Borrar un concepto **pagado** (con `detalles_recibo`) deja líneas huérfanas: la línea de cobro
  desaparece de la reimpresión del recibo y de los reportes de cobranza (el dinero sigue contado en
  `recibos.total`/caja; se pierde la trazabilidad por concepto)
- El filtro `id_cuota` no null **excluye los consumos de venta**, pero **no** excluye cuotas ya pagadas
  (una cuota pagada conserva `cargo > 0` con `saldo = 0`). Por decisión del usuario no se agregó candado;
  el uso queda restringido por criterio a cuotas de membresía pendientes. El "Deshacer" (conserva el
  `id`) mitiga el error dentro de la sesión de edición

---

## Cambio — Activación de anualidad por fecha exacta (no por mes)
**Fecha:** 2026-06-17

La activación inmediata mostraba una membresía como `ANU` desde que entraba el **mes** de inicio,
aunque su día de inicio fuera futuro (p. ej. inicio 18/06 ya aparecía como anual el 17/06). Se cambió
a comparación **por fecha exacta** para evitar la confusión.

#### `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php`
- Activación inmediata: ahora sólo activa la anualidad si `fecha_inicio <= hoy <= fecha_fin` (fecha
  exacta). Una anualidad con inicio futuro **no** se activa al registrarse.

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- El panel "Cargos incluidos en la anualidad" vuelve a evaluar la vigencia por **fecha exacta**, en
  línea con la activación.

> Nota: con fecha exacta, una anualidad con inicio futuro dentro del mes en curso, registrada después
> de que ya corrió la carga masiva de ese mes, no se auto-activará el día de inicio (no hay proceso
> diario). Las de inicio en un mes futuro sí se activan con la carga masiva de ese mes.

---

## Fix — Las vistas de estado de cuenta sólo reflejaban una anualidad
**Fecha:** 2026-06-17

Un socio puede tener varias anualidades activas a la vez (una por cada membresía que entró en
anualidad). Las vistas de recepción sólo mostraban una. Cambios **únicamente de vista** (la lógica
de activación y los datos no se tocaron):

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- `mount()`: "Cargos incluidos en la anualidad" ahora carga los detalles de **todas** las anualidades
  vigentes del socio (antes usaba `->first()`, mostraba sólo una). La vigencia se evalúa **por mes**,
  igual que la activación (`CargosController::activarAnualidad` / `Anualidad::activar`), para que el
  panel coincida con el estado `ANU` de las membresías.
- Nuevo computed `membresiasSocio()`: devuelve las membresías no canceladas con su **estado real**
  desde `socios_membresias` (fuente de verdad), no el tipo de la cuota fija.

#### `resources/views/livewire/recepcion/estados/cargos-nuevo.blade.php`
- La cabecera "Membresías:" ahora itera `membresiasSocio` y muestra el estado real, incluyendo las
  membresías en `ANU` que ya no tienen cuota fija asociada (antes se derivaban de `socios_cuotas`, por
  lo que las ANU sin cuota no aparecían y las ANU con cuota se mostraban como MEN).

#### `resources/views/livewire/recepcion/estados/principal.blade.php`
- La columna de membresías muestra el estado real desde `socios_membresias`: una membresía en `ANU`
  que aún conserva su cuota mensual se muestra como `ANU` (antes mostraba el tipo de la cuota = `MEN`).

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

---

## Fase 4.2 — Option B: principal en ambas tablas + refactor de facturación
**Fecha:** 2026-05-29

### Contexto
Implementación completa de la "Opción B": la membresía principal ahora vive en `socios_membresias` (estado/referencia) **y** en `socios_cuotas` (cobros y tarifa personalizada). `socios_cuotas` pasa a ser la fuente única de cobros para todas las membresías.

### Archivos creados

#### `database/migrations/2026_05_29_152713_backfill_principal_into_socios_cuotas.php`
- Inserta en `socios_cuotas` la fila de membresía principal para socios que solo la tenían en `socios_membresias`
- Filtros: `sm.estado != 'CAN'`, `JOIN socios` para excluir huérfanos, `NOT EXISTS` para no duplicar
- `down()` revierte las filas insertadas

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- `store()`: tras crear la fila en `socios_membresias`, crea también la fila en `socios_cuotas` con `auto_delete=true`
- `update()`: eliminados pasos 2 y 3 (mover principal entre tablas). Ahora itera `$candidatosActivos` (principal + adicionales) en lugar de solo `$nuevosAdicionales` — corrige bug donde el estado de la principal no se actualizaba en `socios_cuotas`
- `update()`: eliminada variable `$claveMovidaAdicional` que causaba `Undefined variable` en PHP 8
- `comprobarMultiples()`: corregido bug donde el flujo de nuevo socio sobreescribía `claves_membresia` con array vacío al reconstruir desde `estados_membresia` (que siempre está vacío en registro nuevo)

#### `app/Http/Controllers/CargosController.php`
- `cargarMensualidades()`: eliminada lógica de IDs hardcodeados. Ahora lee dinámicamente `socios_cuotas` de cada socio y usa `monto_a_cobrar` (respeta `monto_personalizado`)
- Idempotencia por `groupBy + count`: si el cargo ya fue emitido este mes no se duplica
- Eliminado método privado `contarCargos()`

#### `app/Models/Socio.php`
- `sincronizarMembresiaLegacy()`: eliminado `$mayor->delete()` — con Option B la fila promovida debe permanecer en `socios_cuotas`

---

## Mejoras de UI — Visualización de membresías en múltiples pantallas
**Fecha:** 2026-05-29

### Archivos modificados

#### `app/Exports/SociosExport.php`
- Eliminada columna **"CLAVE MEMBRESIA"** (redundante con "MEMBRESIAS CONTRATADAS")

#### `app/Exports/RecibosExport.php`
- `tipoCuota()` reescrito: ahora consulta `socios_cuotas` y devuelve **todas** las claves de membresía del socio separadas por coma (ej. `CG-I, CC-F`). Antes devolvía solo los primeros 2 caracteres de la principal
- Eliminado uso de `ReciboMembresia` (caché histórico de una sola clave)

#### `app/Livewire/Recepcion/Socios.php`
- Agrega `cuotasMembresia.cuota` al eager loading

#### `app/Livewire/Recepcion/Estados/Principal.php`
- Agrega `cuotasMembresia.cuota` al eager loading

#### `resources/views/livewire/recepcion/socios.blade.php`
- Columna MEMBRESÍA ahora lista todas las membresías del socio desde `cuotasMembresia` (una por línea). Fallback a `socioMembresia` para socios sin entradas en `socios_cuotas` (ej. INT)

#### `resources/views/livewire/recepcion/estados/principal.blade.php`
- Columna MEMBRESÍA ahora muestra todas las membresías con su estado (clave + tipo). Fallback igual al anterior

#### `resources/views/livewire/recepcion/estados/cargos-nuevo.blade.php`
- Sección de membresía en el header ahora muestra todas las membresías del socio filtradas desde `$listaCargosFijos` (cuotas con `clave_membresia`). Etiqueta "Membresías:" añadida

#### `resources/views/livewire/recepcion/socios-editar.blade.php`
- Eliminado badge **"Principal"** del dropdown de membresías

#### `resources/views/livewire/acceso/socios/principal.blade.php`
- Si el socio tiene más de 1 membresía muestra **"Membresías múltiples"**; si tiene 1 muestra la clave
- Sección **"Estado membresía:"** separada mostrando el estado de `socios_membresias`

---

## Fase 4.1 — Página "Editar Cuotas" en Sistemas
**Fecha:** 2026-05-29

### Contexto
Página exclusiva del módulo Sistemas que permite configurar `monto_personalizado` por cuota de cada socio. Cubre membresías y cargos fijos (lockers, resguardo).

### Archivos creados

#### `app/Livewire/Sistemas/Recepcion/EditarCuotas.php`
- Carga todas las `socios_cuotas` del socio con `cuotasMembresia.cuota`
- Método `guardar()`: valida `nullable|numeric|min:0|max:99999999.99`, guarda en transacción con `try/catch`
- Método `limpiar($index)`: pone `monto_personalizado = null` para la fila indicada (revierte al precio base)
- Mensaje de éxito (`session flash`) y mensaje de error visible si el guardado falla

#### `resources/views/livewire/sistemas/recepcion/editar-cuotas.blade.php`
- Tabla con columnas: Concepto, Tipo, Precio base, Precio personalizado (editable), Precio efectivo
- Input resaltado en morado si la cuota ya tiene precio personalizado
- Botón "Limpiar" por fila (aparece solo si hay precio personalizado)
- Alertas de éxito (verde) y error (rojo)
- Descripción explicativa del funcionamiento de la página

#### `resources/views/sistemas/Recepcion/editar-cuotas.blade.php`
- Layout con nav de Sistemas y título "EDITAR CUOTAS"

### Archivos modificados

#### `app/Http/Controllers/SistemasController.php`
- Método `editarCuotas(Socio $socio)`: devuelve la vista pasando el socio con model binding

#### `routes/web.php`
- Nueva ruta `GET /sistemas/recepcion/editar-cuotas/{socio}` → `sistemas.editar-cuotas`

#### `resources/views/livewire/sistemas/recepcion/socios/lista-socios.blade.php`
- Botón de edición (ícono lápiz) por socio activo que navega a la página de editar cuotas
- Corregido crash `Attempt to read property "membresia" on null` usando `?->` en la columna MEMBRESÍA

---

## Fix — Índice único eliminado de `socios_cuotas`
**Fecha:** 2026-05-29

### Problema
El índice único `(id_socio, id_cuota)` agregado en Fase 1 impedía que un socio tuviera múltiples lockers o resguardos de carrito, funcionalidad que el sistema sí permitía antes de este proyecto.

### Archivos modificados

#### `database/migrations/2026_05_22_142722_alter_socios_cuotas_add_monto_personalizado.php`
- Eliminado el `DELETE` que borraba duplicados antes de crear el índice único
- Eliminada la creación del índice único `socios_cuotas_socio_cuota_unique`
- Eliminada del `down()` la instrucción de eliminar dicho índice

#### `database/migrations/2026_05_29_175555_drop_unique_index_from_socios_cuotas.php` *(nuevo)*
- `up()`: crea índice simple en `id_socio` para soporte de FK, luego elimina el índice único
- `down()`: restaura el índice único y elimina el índice simple

### Impacto
- Socios con múltiples lockers o resguardos en producción ya no perderán registros al migrar
- La unicidad de membresías sigue garantizada por lógica de aplicación en `SocioForm`

---

## Fix — Compatibilidad de migraciones con producción
**Fecha:** 2026-06-04

### Problema
Dos migraciones fallaban al ejecutarse en producción por diferencias entre el entorno local y el de producción.

### Archivos modificados

#### `database/migrations/2026_05_29_175555_drop_unique_index_from_socios_cuotas.php`
- Reemplazado `Schema::getIndexes()` (no existe en Laravel 10) por `DB::select("SHOW INDEX FROM socios_cuotas WHERE Key_name = '...'")`
- El índice solo se elimina si existe, evitando error en producción donde nunca fue creado

#### `database/migrations/2026_05_22_142722_alter_socios_cuotas_add_monto_personalizado.php`
- Reemplazado `->change()` (requiere `doctrine/dbal`, no instalado) por `DB::statement("ALTER TABLE socios_cuotas MODIFY ...")`
- `down()` corregido de la misma forma

---

## Fase A — Backfill adicionales en `socios_membresias`
**Fecha:** 2026-06-04

### Contexto
Con la nueva arquitectura todas las membresías del socio (no solo la principal) deben tener fila en `socios_membresias`. Esta migración sincroniza los datos históricos existentes.

### Archivos creados

#### `database/migrations/2026_06_04_000000_backfill_adicionales_into_socios_membresias.php`
- Inserta en `socios_membresias` las membresías adicionales que existen en `socios_cuotas` pero no tienen fila correspondiente
- Filtros: `clave_membresia IS NOT NULL`, `clave_membresia != 'N/A'`, `tipo IN ('MEN','INA','ANU')` (evita estados inválidos como `EST`)
- Guardia `NOT EXISTS` para evitar duplicados
- Resultado: 12 filas insertadas — todos los socios con membresías adicionales quedaron sincronizados
- `down()`: no reversible automáticamente (sin marca distintiva); restaurar desde respaldo

---

## Fase B — Modelo `Socio`: arquitectura de múltiples membresías
**Fecha:** 2026-06-04

### Archivos modificados

#### `app/Models/Socio.php`
- **Agregada** relación `socioMembresias()` HasMany: devuelve todas las membresías del socio desde `socios_membresias`
- **Modificada** `socioMembresia()` HasOne → accessor de compatibilidad con orden `FIELD(estado,'CAN') ASC, id ASC`: prioriza membresías activas sobre canceladas, nunca devuelve `null` (evita crash en POS y ReportesController que acceden a `->estado` sin `?->`)
- **Eliminado** `calcularPrincipalPorValor()`: concepto de "membresía principal" desaparece con la nueva arquitectura
- **Eliminado** `sincronizarMembresiaLegacy()` y la bandera `$sincronizando`: ya no se necesita sincronización legacy

---

## Fase D — Observer desactivado
**Fecha:** 2026-06-04

### Contexto
`SocioCuotaObserver` invocaba `sincronizarMembresiaLegacy()`, método eliminado en Fase B. Se desregistra para evitar errores.

### Archivos modificados

#### `app/Providers/AppServiceProvider.php`
- Eliminada la línea `SocioCuota::observe(SocioCuotaObserver::class)` y sus imports

#### `app/Observers/SocioCuotaObserver.php`
- Vaciado de lógica — ya no invoca métodos eliminados del modelo

---

## Fase C — Refactor de `SocioForm`: múltiples membresías en ambas tablas
**Fecha:** 2026-06-04

### Contexto
`SocioForm` pasa a escribir cada membresía en `socios_membresias` (estado) **y** `socios_cuotas` (cobro) simultáneamente. Se elimina la lógica de rotación de principal (~60 líneas).

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- **`setSocio()`**: lee membresías directamente de `socioMembresias` (fuente de verdad del estado) en lugar de combinar principal + adicionales de dos tablas. Agrega propiedad `$claves_originales` con las claves al momento de abrir el formulario
- **`store()`**: crea una fila en `socios_membresias` (estado `MEN`) **y** en `socios_cuotas` por **cada** membresía seleccionada. Eliminada la distinción principal vs adicionales
- **`update()` — cancelación total**: borra todas las filas de `socios_membresias` excepto la de mayor monto (queda como `CAN`); borra todas las de `socios_cuotas`. Usa `(id_socio, clave_membresia)` como clave de upsert
- **`update()` — caso normal**: upsert por `(id_socio, clave_membresia)` en `socios_membresias` para cada activa; borra de ambas tablas las que ya no son activas (cancelación parcial sin rastro individual)
- Agregada propiedad `$claves_originales = []`

#### `resources/views/livewire/recepcion/socios-editar.blade.php`
- Opción "Seleccionar" deshabilitada para membresías ya guardadas (`@disabled(in_array($clave, $form->claves_originales))`)
- Texto de ayuda actualizado: indica que "Cancelada" es la única vía para dar de baja una membresía guardada

---

## Fase E — `CargosController`: soporte de múltiples membresías
**Fecha:** 2026-06-04

### Archivos modificados

#### `app/Http/Controllers/CargosController.php`

**`cargarMensualidades()`**
- Reemplazado `SocioMembresia::...->get()` por `->distinct()->pluck('id_socio')`: evita procesar el mismo socio N veces cuando tiene múltiples membresías
- Eliminado import `Builder` (ya no usado)

**`calcularRecargos()`**
- Mismo fix DISTINCT que `cargarMensualidades()`
- Corregida la query mal estructurada con `orWhere` al inicio (se reemplazó por `whereNot` directo)

**`cargarDiferencias()`**
- Reescrita la query: `GROUP BY id_socio + MAX(consumo_minimo)` por socio entre sus membresías MEN/ANU activas. Antes solo tomaba la membresía principal
- Decisión de negocio aplicada: socios ANU también reciben cargo por consumo mínimo (igual que MEN); socios INA quedan excluidos
- Agregada idempotencia obligatoria: verifica existencia del cargo antes de crearlo, evita duplicados al ejecutar dos veces en el mismo mes (bug preexistente)

**`activarAnualidad()`**
- Busca la fila exacta por `(id_socio, clave_mem_f)` en lugar de `->first()` arbitrario

**`desactivarAnualidad()`**
- Mismo fix que `activarAnualidad()`
- Eliminada la línea redundante `$socio_membresia->clave_membresia = $anualidad->clave_mem_f`

---

## Fase F — UI, acceso y validación de ventas
**Fecha:** 2026-06-04

### Archivos modificados

#### `app/Livewire/Acceso/Socios/Principal.php`
- Eager load cambiado a `socioMembresias.membresia` (plural)

#### `resources/views/livewire/acceso/socios/principal.blade.php`
- Display de membresía usa `socioMembresias` (muestra todas con sus estados)
- Lógica de acceso: **ACCESO PERMITIDO** si `socioMembresias->where('estado','!=','CAN')->count() > 0`

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- `mount()`: usa orden `FIELD(estado,'CAN') ASC` para obtener la membresía de compatibilidad
- `mount()`: check ANU usa `SocioMembresia::where(...)->where('estado','ANU')->exists()` (cualquier membresía ANU, no solo la primera)
- Agregadas computed properties `todasCanceladas()` y `tieneAnualidad()`
- `addCuota()`: valida que la cuota corresponda a **cualquier** membresía activa del socio, no solo a la primera

#### `resources/views/livewire/recepcion/estados/cargos-nuevo.blade.php`
- Badge "Anualidad activa" usa `$this->tieneAnualidad`
- Deshabilitar formulario usa `$this->todasCanceladas`

#### Validación de ventas — 4 archivos (bug crítico corregido)
Bug: `SocioMembresia::where('id_socio',...)->first()` devolvía fila arbitraria; un socio con CAN+MEN podía ser bloqueado de comprar. Corregido en:
- `app/Livewire/Recepcion/Ventas/Nueva/SearchBar.php`
- `app/Livewire/Recepcion/Ventas/Nueva/PagosModalBody.php`
- `app/Livewire/Forms/VentaForm.php` (`setSocioPago`)
- `app/Livewire/Puntos/Ventas/Nueva/Container.php` (POS)

Nueva lógica en los 4 archivos: `whereNot('estado','CAN')->exists()` — acceso si existe al menos una membresía activa

---

## Fase G — Exports actualizados para múltiples membresías
**Fecha:** 2026-06-04

### Archivos modificados

#### `app/Exports/SociosExport.php`
- Eliminado `JOIN socios_membresias` que duplicaba socios con múltiples membresías
- Reemplazado por `Socio::with('socioMembresias')->whereHas('socioMembresia')->get()`
- Columna **ESTADO**: lista todos los estados del socio separados por coma (ej. `INA, MEN`)
- Columna **MEMBRESIAS CONTRATADAS**: usa `socioMembresias->pluck('clave_membresia')->unique()->implode(', ')`
- Método `listarMembresias()` actualizado para recibir el modelo `Socio` directamente

#### `app/Exports/RecibosExport.php`
- Fallback `SocioMembresia::where('id_socio',...)->first()` en `tipoCuota()` reemplazado por query con orden `FIELD(estado,'CAN') ASC, id ASC` — devuelve la membresía más relevante

#### `app/Exports/CarteraVencidaExport.php`
- Sin cambios de código — lógica ya compatible con la nueva arquitectura. Verificado sin regresiones

---

## Fase H — Pantalla de anualidades: visualización de membresías
**Fecha:** 2026-06-04

### Contexto
Sin tocar la lógica del módulo de anualidades, se agrega solo la visualización de las membresías activas del socio. Las Fases 4.5A y 4.6 (dropdown filtrado y vinculación por membresía individual) quedan pendientes por restricción del equipo.

### Archivos modificados

#### `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php`
- Agregada computed property `membresiasActivas()`: devuelve las membresías no canceladas del socio desde `socios_membresias`

#### `resources/views/livewire/sistemas/recepcion/anualidad/nueva.blade.php`
- Reemplazado el display estático "Membresía / Estado" por una lista con todas las membresías activas del socio (una por línea con su estado)

---

## Mejoras de UI y correcciones de precios personalizados
**Fecha:** 2026-06-05

### Contexto
Correcciones al flujo de cobros manuales y automáticos para que respeten `monto_personalizado` en todos los puntos del sistema. Mejoras de visualización en varias pantallas.

---

### Visualización de membresías en pantallas de socios

#### `resources/views/livewire/puntos/socios/container.blade.php`
- Reemplazado display de membresía única (con badges de color) por lista de todas las membresías del socio con nombre completo y estado en texto plano: `• Descripción (ESTADO)`
- Lógica de acceso actualizada a `socioMembresias->where('estado','!=','CAN')->count() > 0`

#### `app/Livewire/Recepcion/Socios.php`
- Eager load cambiado de `cuotasMembresia.cuota` a `socioMembresias.membresia`

#### `resources/views/livewire/recepcion/socios.blade.php`
- Columna MEMBRESÍA ahora lista todas las membresías desde `socioMembresias` mostrando `membresia->descripcion` (nombre completo) en lugar de `clave_membresia` abreviada

---

### Nombre completo de membresía en nuevo cargo

#### `app/Models/Cuota.php`
- Agregada relación `membresia()` BelongsTo a `Membresias` via `clave_membresia → clave`

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- `obtenerCargosFijos()`: eager load cambiado de `cuota` a `cuota.membresia`

#### `resources/views/livewire/recepcion/estados/cargos-nuevo.blade.php`
- Sección de membresías del header muestra `membresia->descripcion` (nombre completo) en lugar de `clave_membresia`. Fallback al código si no hay relación

---

### Ajustes en tabla "Editar Cuotas" (Sistemas)

#### `resources/views/livewire/sistemas/recepcion/editar-cuotas.blade.php`
- Columna "Concepto" renombrada a "Cuota"
- Columna "Precio efectivo" eliminada (redundante con "Precio personalizado")
- `colspan` del estado vacío corregido de 6 a 5

---

### Precio personalizado en Cargos fijos

#### `resources/views/livewire/recepcion/estados/include/cargos-fijos.blade.php`
- Columna CARGOS ahora muestra `monto_personalizado ?? monto_base` con `number_format`. Antes siempre mostraba el precio base del catálogo

---

### Corrección de precios en cobros manuales (`addCuota`)

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- `addCuota()`: antes de agregar el cargo al array, consulta `socios_cuotas` por `(id_socio, id_cuota)` y sobreescribe `monto` con `monto_personalizado` si existe. Corrige que el cargo manual siempre usara el precio base

---

### Botón "Cargar" por fila en Cargos fijos

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- Agregado método `cargarDesdeCargoFijo(int $indexFijo)`: agrega al array de cargos usando exactamente la fila de `socios_cuotas` indicada con su `monto_personalizado`
- Validaciones: fecha requerida, no duplicar membresía en el mes (con `dispatch('action-message-cargos')` para que el error sea visible), no duplicar la misma fila de `socios_cuotas` en el mes (identificada por `socios_cuota_id` en lugar de `id_cuota`, lo que permite cargar dos lockers con distinto precio de forma independiente)

#### `resources/views/livewire/recepcion/estados/include/cargos-fijos.blade.php`
- Botón **Cargar** agregado a cada fila de la tabla. Llama a `cargarDesdeCargoFijo($indexFijo)` para cargar el precio correcto de cada fila sin ambigüedad

---

### Fix: `cargarMensualidades()` duplicaba precio en cuotas repetidas

#### `app/Http/Controllers/CargosController.php`
- **Bug corregido**: el loop usaba `$rows->first()` en cada iteración, haciendo que socios con dos lockers (o resguardos) de distinto `monto_personalizado` recibieran ambos cargos con el precio del primero
- **Fix**: reemplazado el `for` + `$rows->first()` por `foreach ($rows->values()->slice($enEstado) as $sc)`: itera cada fila individualmente, saltando las ya cobradas, y usa su propio `monto_a_cobrar`

---

## Fix — Aislamiento por socio en `cargarMensualidades()`
**Fecha:** 2026-06-10

### Problema detectado
`cargarMensualidades()` envolvía el procesamiento de **todos** los socios en una sola transacción. Si `activarAnualidad()`/`desactivarAnualidad()` lanzaban una excepción para un solo socio (p. ej. un `clave_mem_f` en `anualidades` que ya no coincide con ninguna fila de `socios_membresias` del socio), se revertía el cobro de **todos** los socios del mes y la petición terminaba en un HTTP 500 sin información de cuál socio falló.

### Archivos modificados

#### `app/Http/Controllers/CargosController.php`
- `cargarMensualidades()`: cada socio ahora se procesa en su propia `DB::transaction(..., 2)` dentro de un `try/catch`
- Si un socio falla, su transacción se revierte de forma aislada (no afecta a los demás), el error se registra con `Log::error()` y se acumula
- La respuesta final incluye un resumen de los socios que fallaron (en vez de un 500 genérico) cuando hubo errores

### Pruebas realizadas en local
- Simulación de `cargarMensualidades` para enero 2027 (mes en el que 5 anualidades con `clave_mem_f` desincronizado provocan la excepción), dentro de una transacción revertida al final
- Resultado: los 5 socios afectados quedaron sin cambios y reportados en el mensaje de respuesta; los otros ~263 socios generaron sus 481 cargos de `EstadoCuenta` con normalidad

---

## Fix — Crash en "Nuevo cargo" al crear una anualidad a mitad de mes
**Fecha:** 2026-06-10

### Problema detectado
`CargosNuevo::mount()` forzaba "hoy" al día 1 del mes (`now()->day(1)`) para localizar la anualidad vigente del socio. Si la anualidad recién creada tenía `fecha_inicio` después del día 1 del mes actual (el caso normal al crearla a mitad de mes), la búsqueda no la encontraba aunque hoy sí estuviera dentro de su rango, y `$anualidad->id` lanzaba `Attempt to read property "id" on null`.

### Archivos modificados

#### `app/Livewire/Recepcion/Estados/CargosNuevo.php`
- `mount()`: usa `now()` (fecha real de hoy) en vez de `now()->day(1)` para localizar la anualidad vigente
- Agregado chequeo defensivo: si no se encuentra una anualidad vigente, `cargos_anualidad` queda vacío en lugar de crashear

### Verificación en local
- Reproducido y corregido con el socio 7856 (anualidad #123, `fecha_inicio = 2026-06-10`): `mount()` ahora encuentra la anualidad y carga su detalle correctamente

---

## Fix — `SocioForm` recreaba el cargo fijo de membresías en ANU (doble cobro / línea fantasma)
**Fecha:** 2026-06-16

### Problema detectado
Al guardar la edición de un socio (`SocioForm::update()`), el paso de sincronización de `socios_cuotas` recreaba el cargo fijo de **cualquier** membresía activa que no lo tuviera, incluidas las que están en estado `ANU`. Como la activación de la anualidad elimina ese cargo a propósito (la mensualidad está prepagada en la anualidad), editar al socio durante la anualidad lo regeneraba. El proceso mensual volvía a generar un cargo de esa membresía cada mes — un doble cobro silencioso (o una línea fantasma de `$0.00` cuando la cuota tipo `ANU` vale 0).

### Archivos modificados

#### `app/Livewire/Forms/SocioForm.php`
- En el paso 2 de la sincronización de `socios_cuotas` dentro de `update()`: si la membresía está en estado `ANU`, **no** se recrea su cargo fijo y, si quedó uno de una edición previa, se elimina (auto-reparador). Las membresías `MEN`/`INA` siguen procesándose igual que antes
- Una membresía en anualidad no debe tener cargo fijo mensual: se elimina al activar la anualidad y se reconstruye desde `detalles_anualidades` al finalizarla

### Verificación en local
- Socio 7869 (CG-F en `ANU` con fila fantasma en `socios_cuotas`): tras el fix, guardar la edición del socio elimina la fila fantasma y ya no la recrea; las 4 membresías `MEN` quedan intactas. Probado con `Livewire::test` dentro de transacción revertida

---

## Panel de gestión de cargos fijos en "Cargar Anualidad" (Sistemas)
**Fecha:** 2026-06-16

### Contexto
En `sistemas/recepcion/cargo-anualidades`, al seleccionar un socio se muestra un apartado con sus cargos fijos (`socios_cuotas`). Permite, como parte del alta de la anualidad, **borrar cuotas** (locker/resguardo y la cuota de la membresía que entra en anualidad) y **cancelar (CAN) otras membresías**. Todos los cambios marcados son **diferidos**: no tocan la BD hasta que el proceso mensual **activa** la anualidad (mes de `fecha_inicio`), consistente con el ciclo de vida de anualidades.

### Archivos creados

#### `database/migrations/2026_06_12_000000_add_cuotas_fijas_eliminar_to_anualidades_table.php`
- Columna `cuotas_fijas_eliminar` (`json`, nullable) en `anualidades`: ids de `socios_cuotas` a eliminar al activar la anualidad

#### `database/migrations/2026_06_12_000100_add_membresias_cancelar_to_anualidades_table.php`
- Columna `membresias_cancelar` (`json`, nullable) en `anualidades`: claves de membresía a cancelar (CAN) al activar la anualidad

### Archivos modificados

#### `app/Models/Anualidad.php`
- Agregado `$casts` con `cuotas_fijas_eliminar => array` y `membresias_cancelar => array`

#### `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php`
- Propiedades `#[Locked]`: `listaCargosFijos`, `listaCargosFijosEliminados`, `listaMembresiasCancelar`
- `selectedSocio()` carga los cargos fijos del socio; `cargarCargosFijos()` resetea las marcas del socio anterior
- Ruteo por tipo de cargo:
  - Membresía que entra en la anualidad (la elegida en "Membresía al finalizar") → `removerCargoFijo()` borra su cuota (la membresía pasa a `ANU`)
  - Otras membresías → `cancelarMembresia()` las marca para `CAN`
  - Cargos sin membresía (locker/resguardo) → `removerCargoFijo()` borra la cuota
- `removerTodosCargosFijos()` rutea cada cargo a la acción correspondiente; `restaurarCargosFijos()` deshace lo marcado
- Guardia: no se puede cancelar (ni borrar como cuota) la membresía que entra en la anualidad por el camino equivocado; bloqueo en el componente y red de seguridad en el controlador
- `filtrarCuotasEliminar()`: al aplicar, descarta de `cuotas_fijas_eliminar` cuotas de membresías que ya no son la de la anualidad (p. ej. si se cambió el select), evitando borrados no durables que `SocioForm` recrearía
- `aplicarAnualidad()` guarda `cuotas_fijas_eliminar` y `membresias_cancelar` en la anualidad

#### `app/Http/Controllers/CargosController.php`
- `activarAnualidad()`: al activar la anualidad, además de poner la membresía en `ANU`, elimina las cuotas marcadas (`cuotas_fijas_eliminar`, por id, idempotente con `eliminarCargosAnteriores`) y cancela las membresías marcadas (`membresias_cancelar`): pone su `socios_membresias.estado = 'CAN'` y borra sus `socios_cuotas`. Nunca cancela la membresía que entra en la anualidad (`clave_mem_f`)

#### `resources/views/livewire/sistemas/recepcion/anualidad/nueva.blade.php`
- Apartado "Cargos fijos del socio" reubicado abajo, en rejilla de 2 columnas (columna derecha reservada para uso futuro), visible sólo con socio seleccionado
- Acción por fila con ícono de papelera: "Cancelar membresía" (otras membresías) o "Borrar cuota" (cargos sin membresía y la membresía de la anualidad, con título "Borrar cuota (entra en anualidad)")
- Botón "Marcar todos" + aviso ámbar con resumen de lo marcado y botón "Deshacer"
- Sin diálogos de confirmación en los botones (papeleras y "Marcar todos")
- Select "Membresía al finalizar" cambiado a `wire:model.live` para que la fila correcta cambie a "Borrar cuota" al elegirla

### Comportamiento (timing)
- Borrado de cuotas y cancelación de membresías ocurren **cuando inicia la anualidad** (al correr el proceso del mes de `fecha_inicio`), no al pulsar "Aplicar anualidad". Hasta entonces los cargos se siguen cobrando normalmente
- Junto con el fix de `SocioForm` (membresías `ANU` no se recrean), las eliminaciones quedan **durables**

### Verificación en local (socio 7869, `Livewire::test` + activación simulada, todo en transacción revertida)
- Borrar la cuota de la membresía de la anualidad (CC-F) + cancelar otra (CC-P): al activar, CC-F → `ANU` sin cuota y CC-P → `CAN` sin cuota; las demás intactas
- Guardia: intentar borrar la cuota de una membresía que no es la de la anualidad no marca nada
- Trampa: marcar la cuota de CC-F y luego cambiar la membresía de la anualidad a CC-I → `cuotas_fijas_eliminar` queda en `null` (CC-F descartada)

---

## Activación inmediata de anualidad ya vigente al registrarla
**Fecha:** 2026-06-16

### Contexto
Al registrar una anualidad cuyo periodo **ya incluye el mes actual** (inicio retroactivo o del mes en curso), debe aplicarse al guardar, sin esperar a la carga masiva de mensualidades: poner la membresía en `ANU`, borrar las cuotas marcadas y cancelar las membresías marcadas. Caso clave: una anualidad con `fecha_inicio` en un mes ya pasado nunca sería activada por el proceso mensual (que sólo activa en el mes exacto de `fecha_inicio`).

### Archivos modificados

#### `app/Models/Anualidad.php`
- Nuevo método `activar()`: aplica los efectos de iniciar la anualidad (membresía `clave_mem_f` → `ANU`, borrado de `cuotas_fijas_eliminar`, cancelación `CAN` de `membresias_cancelar` y borrado de sus cuotas). Lanza excepción si no existe la fila de `socios_membresias`. No genera mensualidades. Reutilizable por el proceso mensual y por la activación inmediata

#### `app/Http/Controllers/CargosController.php`
- `activarAnualidad()` refactorizado para delegar en `$anualidad->activar()` (misma lógica, sin duplicación)

#### `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php`
- `aplicarAnualidad()`: tras crear la anualidad, si su periodo (comparado por mes, igual que el proceso mensual) incluye el mes actual, llama a `$anualidad->activar()` dentro de la misma transacción. Si el inicio es futuro, no se activa ahora — queda guardado (`cuotas_fijas_eliminar`/`membresias_cancelar`) para que el proceso mensual lo aplique en su mes

### Verificación en local (socio 7869, `Livewire::test` + transacción revertida)
- **Retroactivo** (inicio 2026-02-16, hoy 2026-06-16): al guardar, la membresía de la anualidad pasó a `ANU` sin cuota y la membresía marcada quedó en `CAN` sin cuota — sin correr la carga masiva
- **Futuro** (inicio 2026-08-16): no se activó al guardar; las marcas quedaron almacenadas en la anualidad
- **Proceso mensual** (refactor): correr `activarAnualidad` para agosto activó la anualidad futura correctamente — sin regresión

---

## Fix — La carga masiva de mensualidades no aplicaba el texto concatenado de la cuota
**Fecha:** 2026-06-16

### Problema detectado
Una cuota fija con `texto_concepto`/`posicion_texto` (texto personalizado concatenado al concepto) sólo reflejaba ese texto al cargarse manualmente desde `recepcion/edo-cuenta/nuevo-cargo`. La carga masiva (`sistemas/recepcion/cargo-mensualidades` → `CargosController::cargarMensualidades`) construía el concepto como `descripcion mes-año` **sin** aplicar `texto_concepto`, por lo que el texto no aparecía en el estado de cuenta. El precio personalizado (`monto_personalizado`) sí se aplicaba en ambos flujos; sólo faltaba el texto en el masivo.

### Archivos modificados

#### `app/Models/SocioCuota.php`
- Nuevo método `aplicarTextoConcepto(string $descripcionBase): string`: concatena `texto_concepto` a la izquierda o derecha de la descripción base según `posicion_texto` (default `izquierda`); si no hay texto, devuelve la base sin cambios. Centraliza el formato para que carga manual y masiva sean idénticas

#### `app/Http/Controllers/CargosController.php`
- `cargarMensualidades()`: el concepto del `EstadoCuenta` ahora se construye con `$sc->aplicarTextoConcepto($descripcionBase)` en lugar de sólo `descripcion mes-año`

### Verificación en local
- Unit del método: `izquierda` → `BECADO MENSUALIDAD CG-FAMILIAR JUNIO-2026`; `derecha` → `... JUNIO-2026 BECADO`; sin texto → base sin cambios
- Integración (socio 1057, transacción revertida): la carga masiva generó `BECADO MEN.INACTIVA CG-FAMILIAR JUNIO-2026` con `cargo = 1234.50` (texto **y** precio personalizado aplicados)
