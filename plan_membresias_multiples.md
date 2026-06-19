# Plan: Múltiples Membresías en `socios_membresias`

**Fecha:** 2026-06-03
**Estado:** Pendiente (revisado v3 — validado empíricamente contra la BD)

---

## Objetivo

Cambiar la arquitectura de membresías para que **todas** las membresías de un socio
(no solo la principal) estén registradas en `socios_membresias`, con su propio estado
individual (MEN/INA/ANU/CAN). Actualmente `socios_membresias` tiene una sola fila por
socio (la "principal").

---

## Contexto

### Arquitectura actual
| Tabla | Contenido |
|---|---|
| `socios_membresias` | 1 fila por socio — membresía principal + su estado |
| `socios_cuotas` | Todas las membresías (principal + adicionales) + cargos fijos |

### Arquitectura nueva
| Tabla | Contenido |
|---|---|
| `socios_membresias` | **N filas por socio** — una por membresía, cada una con su propio estado |
| `socios_cuotas` | Sin cambio — todas las membresías + cargos fijos |

### Reglas que se mantienen
- El concepto de "membresía principal" **desaparece** — cada membresía actúa de forma independiente.
- El consumo mínimo sigue siendo el **mayor** entre todas las membresías activas del socio.
- El acceso al club se permite si **alguna** membresía tiene estado distinto de CAN.

---

## Invariante crítica: registro de cada membresía

Con esta arquitectura, el registro de una membresía depende de **si está activa o cancelada**
(verificado en datos reales: 296 membresías CAN viven solo en `socios_membresias`, 0 en
`socios_cuotas` — las canceladas se borran de `socios_cuotas`):

| Estado de la membresía | `socios_membresias` | `socios_cuotas` |
|---|---|---|
| **Activa** (MEN / INA / ANU) | ✅ fila con su estado | ✅ fila con la cuota del tipo correspondiente |
| **Cancelada** (CAN) | ✅ fila con estado CAN (rastro histórico) | ❌ no existe (no se cobra) |

| Tabla | Qué define |
|---|---|
| `socios_membresias` | `(clave_membresia, estado)` — **fuente de verdad del ESTADO** (incluye CAN) |
| `socios_cuotas` | `id_cuota` → cuota del tipo correspondiente — **fuente de verdad del COBRO** (solo activas) |

**Regla de sincronización (obligatoria):** Para una membresía **activa**, el `estado` en
`socios_membresias` y el `tipo` de la cuota referenciada en `socios_cuotas` **deben
coincidir**. Al **cancelar** una membresía: se pone `estado=CAN` en `socios_membresias`
y se **elimina** su fila de `socios_cuotas`. Todo código que cambie el estado (SocioForm,
anualidad) debe respetar esta asimetría en la misma transacción. Esta es la principal
fuente de complejidad y de posibles inconsistencias del cambio.

> **Implicación para el backfill (Fase A):** lee desde `socios_cuotas`, por lo que solo
> creará filas para membresías **activas**. Las membresías CAN actuales ya están en
> `socios_membresias` (una por socio) y se conservan tal cual — correcto.

---

## Nota crítica: Aplicación paralela de POS

El módulo `Puntos/` (POS) y `Recepcion/Ventas/` usan `socioMembresia` directamente.
Líneas que acceden a `->estado` **sin** operador de null-safe (`?->`):

```blade
{{-- puntos/socios/container.blade.php:18,21,28 --}}
Estado membresia: {{ $socio ? $socio->socioMembresia->estado : '' }}
Tipo membresia: {{ $socio ? $socio->socioMembresia->membresia->descripcion : '' }}
@if ($socio->socioMembresia->estado == 'CAN')
```

```php
// ReportesController.php:990
} elseif ($socio->socioMembresia->estado != 'CAN') {
```

**Por eso el accessor de compatibilidad NO debe filtrar CAN** — si devolviera `null`
para un socio totalmente cancelado, estas líneas harían crash
(`Attempt to read property "estado" on null`). El accessor debe devolver **siempre**
una fila (igual que hoy), priorizando las no-canceladas.

---

## Esquema

`socios_membresias` **ya no tiene restricción única** en `id_socio` (revisado en
migración original `2024_05_16_123106` — solo define `integer unsigned`, sin `unique()`).
No se requiere cambio de esquema para las membresías múltiples — solo backfill de datos.

(La Fase H/4.6 sí requiere una migración nueva para la tabla `anualidades`.)

---

## Fases de implementación

---

### Fase A — Migración de datos (backfill)

**Archivo:** nueva migración `2026_XX_XX_backfill_adicionales_into_socios_membresias.php`

**Qué hace:** Para cada membresía adicional que existe en `socios_cuotas` pero no
tiene fila en `socios_membresias`, insertar la fila correspondiente. El estado se
deriva de `cuota->tipo`. Los cargos fijos (locker, resguardo) se excluyen porque
tienen `clave_membresia` NULL.

> **⚠ Filtro de estados válidos (verificado):** existe al menos una cuota de membresía
> con `tipo='EST'` (clave EST, ESTÉTICA). Insertar `estado='EST'` rompería el modelo
> (estados válidos: MEN/INA/ANU). El backfill **debe filtrar** `c.tipo IN ('MEN','INA','ANU')`
> y añadir `c.clave_membresia != 'N/A'`. (Hoy las 12 filas que generaría son todas MEN
> y el único socio con EST ya tiene su fila principal, pero el filtro protege casos futuros.)

```sql
INSERT INTO socios_membresias (id_socio, clave_membresia, estado)
SELECT sc.id_socio, c.clave_membresia, c.tipo
FROM socios_cuotas sc
JOIN cuotas c ON sc.id_cuota = c.id
WHERE c.clave_membresia IS NOT NULL
  AND c.clave_membresia != 'N/A'
  AND c.tipo IN ('MEN','INA','ANU')          -- evita estados inválidos (EST, etc.)
  AND NOT EXISTS (
    SELECT 1 FROM socios_membresias sm
    WHERE sm.id_socio = sc.id_socio
      AND sm.clave_membresia = c.clave_membresia
  )
```

**Verificación post-backfill:** cada `(id_socio, clave_membresia)` de `socios_cuotas`
con cuota de tipo MEN/INA/ANU debe tener exactamente una fila en `socios_membresias`.
Confirmar también que no se insertó ningún `estado` fuera de MEN/INA/ANU.

**`down()`:** No reversible automáticamente con seguridad (no hay marca distintiva de
qué filas eran adicionales). Documentar: restaurar desde respaldo.

**Riesgo:** Bajo — solo INSERTs con guardia NOT EXISTS.

---

### Fase B — Modelo `Socio`

**Archivo:** `app/Models/Socio.php`

**Cambios:**

1. **Agregar** relación `socioMembresias()` HasMany — nueva relación principal:
   ```php
   public function socioMembresias(): HasMany
   {
       return $this->hasMany(SocioMembresia::class, 'id_socio');
   }
   ```

2. **Modificar** `socioMembresia()` HasOne → accessor de compatibilidad. **No filtra
   CAN** (para no devolver null). Prioriza no-canceladas con `FIELD(estado,'CAN')`
   (verificado: no-CAN→0, CAN→1, ASC pone no-CAN primero). **Sin join** para evitar
   colisión de columnas `id` entre `socios_membresias` y `cuotas`:
   ```php
   public function socioMembresia(): HasOne
   {
       return $this->hasOne(SocioMembresia::class, 'id_socio')
           ->orderByRaw("FIELD(estado, 'CAN') ASC")
           ->orderBy('id');
   }
   ```
   > Limitación aceptada: entre varias membresías activas devuelve la de menor `id`
   > (no la de mayor monto). Como el concepto de "principal" desaparece y los
   > consumidores solo necesitan el estado, esto es suficiente.

3. **Eliminar** `calcularPrincipalPorValor()` — concepto de principal desaparece.

4. **Eliminar** `sincronizarMembresiaLegacy()` y la bandera `$sincronizando`.

**Riesgo:** Medio — el accessor debe probarse en POS, Recepcion/Ventas y ReportesController.

---

### Fase C — `SocioForm`

**Archivo:** `app/Livewire/Forms/SocioForm.php`

Es el cambio más complejo. Elimina ~60 líneas de lógica de rotación de principal.
**Debe mantener la invariante:** cada membresía activa queda en `socios_membresias`
(con su estado) **y** en `socios_cuotas` (con la cuota del tipo correspondiente).

**`store()`:** (registro nuevo — **el formulario `SociosNuevo` no tiene dropdown de
estado por membresía**: todas las membresías nuevas entran como `MEN`. El pseudocódigo
itera `$cuotasPorClave` (cuotas MEN del catálogo), no `$candidatosActivos`.)
- Crear una fila en `socios_membresias` con estado `MEN` por **cada** membresía
  seleccionada (hoy solo se crea la principal — líneas 308–319).
- Mantener la creación en `socios_cuotas` (ya existe para todas — líneas 314–328).

```php
// $cuotasPorClave = cuotas MEN del catálogo para cada clave seleccionada (ya existe)
foreach ($cuotasPorClave as $cuota) {
    SocioMembresia::create([
        'id_socio'        => $socio->id,
        'clave_membresia' => $cuota->clave_membresia,
        'estado'          => 'MEN',
    ]);
    SocioCuota::create([
        'id_socio'    => $socio->id,
        'id_cuota'    => $cuota->id,
        'auto_delete' => true,
    ]);
}
// Desaparece la distinción principal (first) vs adicionales (skip(1)) de las líneas 306–328.
```

**`update()`:** Debe manejar **dos casos** (el actual ya los separa, líneas 414–478):

**Caso A — cancelación total** (`$candidatosActivos` vacío): conservar **solo la de
mayor monto** como CAN en `socios_membresias`, borrar **todas** las demás filas de
`socios_membresias` y todas de `socios_cuotas`. (Adapta el bloque actual 417–432, que
hoy ya hace esto pero con una sola fila — ahora debe borrar las múltiples.)

```php
if ($candidatosActivos->isEmpty() && $todosLosCandidatos->isNotEmpty()) {
    $mayor = $todosLosCandidatos->first();   // mayor monto
    // socios_membresias: dejar SOLO una fila CAN (la mayor), borrar el resto
    SocioMembresia::where('id_socio', $idSocio)
        ->where('clave_membresia', '!=', $mayor['clave'])->delete();
    SocioMembresia::updateOrCreate(
        ['id_socio' => $idSocio, 'clave_membresia' => $mayor['clave']],
        ['estado' => 'CAN']
    );
    // socios_cuotas: borrar todas las de membresía
    SocioCuota::where('id_socio', $idSocio)
        ->whereIn('id_cuota', Cuota::whereNotNull('clave_membresia')->pluck('id'))->delete();
    $this->socio->update($validated);
    return;
}
```

**Caso B — al menos una activa:** una fila en `socios_membresias` por **cada** activa;
las no-activas (CAN o deseleccionadas) se **borran de ambas tablas** (regla "solo una
CAN": en parcial no se deja rastro). `socios_cuotas` se sincroniza igual que hoy
(líneas 454–474).

```php
$clavesActivas = $candidatosActivos->pluck('clave')->toArray();
foreach ($candidatosActivos as $item) {
    SocioMembresia::updateOrCreate(
        ['id_socio' => $idSocio, 'clave_membresia' => $item['clave']],
        ['estado'   => $item['estado']]
    );
    // sincronizar su fila en socios_cuotas a la cuota del tipo $item['estado'] (ya existe, líneas 454–467)
}
// Borrar de socios_membresias las que ya no están activas (sin rastro — parcial)
SocioMembresia::where('id_socio', $idSocio)
    ->whereNotIn('clave_membresia', $clavesActivas)->delete();
// Borrar de socios_cuotas las claves que ya no están activas (ya existe, líneas 469–474)
```

> Elimina la lógica de rotación de principal (la "nueva principal" de las líneas
> 435–452 ya no aplica — todas las activas se escriben por igual).

**Manejo de cancelación (cambia respecto al comportamiento actual):**

El `update()` actual (líneas 417–432) tiene un "caso cancelación total" que deja **solo
la membresía de mayor monto** como CAN. Con la arquitectura nueva esto cambia:

**Decisión confirmada: "solo una CAN"** — se preserva el comportamiento actual. El
sistema conserva **a lo sumo una fila CAN por socio** (rastro mínimo), no una por
membresía cancelada.

| Caso | Comportamiento |
|---|---|
| Cancelar **una** de varias (parcial, quedan activas) | La membresía cancelada se **borra de ambas tablas** (sin rastro individual), igual que hoy hacen las adicionales. Las activas quedan, cada una con su fila |
| Cancelar **todas** (total) | Se conserva **solo la de mayor monto** como `CAN` en `socios_membresias` (rastro del socio); las demás se borran. Todas fuera de `socios_cuotas`. **(= comportamiento actual, líneas 418–432)** |
| Deseleccionar (volver a "Seleccionar") una membresía que **no estaba guardada** | Se descarta sin rastro (error de captura — nunca se guardó) |

> **Implicación:** "Cancelada" da de baja la membresía siempre; deja rastro `CAN`
> **solo cuando es la última** del socio (cancelación total). En cancelación parcial
> no deja rastro individual — esto mantiene el máximo de una fila CAN por socio y
> replica el comportamiento actual.

**Regla de UI confirmada — "Seleccionar" deja de duplicar a "Cancelada":**

El formulario debe **recordar las claves de membresía que el socio tenía al abrir**
(p. ej. propiedad `$clavesOriginales` poblada en `SocioForm::setSocio()`). Con eso:

| Situación de la membresía | Opción "Seleccionar" | Cómo se quita |
|---|---|---|
| Ya estaba guardada (el socio la tenía) | 🔒 Deshabilitada en el `<option>` | Solo "Cancelada" (rastro CAN solo si es la última — ver "solo una CAN") |
| No estaba guardada (tocada por error esta sesión) | ✅ Habilitada | Volver a "Seleccionar" (sin rastro) |

- En el blade `socios-editar.blade.php`: `<option value="" @disabled(in_array($clave, $clavesOriginales))>Seleccionar</option>`.
- Actualizar el texto de ayuda (línea 273), que hoy dice *"Selecciona 'Seleccionar'
  para deseleccionar..."* — ya no aplica a membresías guardadas.

> El form valida `claves_membresia required|min:1`, así que un socio nunca queda sin
> ninguna membresía. "Cancelada" es la **única** vía para dar de baja una membresía real.

**Riesgo:** Alto — lógica crítica de registro/edición + invariante activa/CAN +
redefinición del caso de cancelación total.

---

### Fase D — Observer

**Archivo:** `app/Observers/SocioCuotaObserver.php` + `AppServiceProvider`

**Cambio:** Eliminar la llamada a `sincronizarMembresiaLegacy()` (método eliminado en
Fase B). Si el observer no tiene otro propósito, desregistrarlo en `AppServiceProvider`.

**Riesgo:** Bajo.

---

### Fase E — `CargosController`

**Archivo:** `app/Http/Controllers/CargosController.php`

#### `cargarMensualidades()`
El loop externo usa `socios_membresias` para listar socios. Con múltiples filas, el
mismo socio entraría N veces → **cobros duplicados**. Cambiar a IDs únicos:

```php
// Antes: get() de objetos SocioMembresia (1 por socio)
// Después: ids únicos
$socios_ids = SocioMembresia::whereNot('estado', 'CAN')
    ->whereNotIn('clave_membresia', ['EVE', 'INT'])
    ->whereHas('socio')
    ->distinct()
    ->pluck('id_socio');

foreach ($socios_ids as $id_socio) {
    // desactivarAnualidad / activarAnualidad / verificarCargosFijos reciben $id_socio (OK)
    // el cobro sigue leyendo socios_cuotas (sin cambio)
}
```
> El cobro ya lee `socios_cuotas` agrupado por `id_cuota` (idempotente). Sin cambio ahí.

#### `cargarRecargos()`
Mismo problema (mismo socio múltiples veces) → mismo fix con `distinct()->pluck('id_socio')`.

#### `cargarDiferencias()`
Reescribir para `MAX(consumo_minimo)` por socio entre sus membresías activas:

```php
$socios = SocioMembresia::whereNot('estado', 'CAN')   // ⚠ ver decisión pendiente abajo
    ->whereHas('socio')
    ->join('membresias', 'socios_membresias.clave_membresia', '=', 'membresias.clave')
    ->select('socios_membresias.id_socio', DB::raw('MAX(membresias.consumo_minimo) as consumo_minimo'))
    ->groupBy('socios_membresias.id_socio')
    ->having('consumo_minimo', '>', 0)
    ->get();
```

**Idempotencia (OBLIGATORIA — bug preexistente):** antes de crear el cargo de
diferencia, verificar que no exista ya uno para ese socio en el período:
```php
$yaExiste = EstadoCuenta::where('id_socio', $idSocio)
    ->where('id_cuota', $cuota->id)
    ->whereYear('fecha', $fecha->year)
    ->whereMonth('fecha', $fecha->month)
    ->exists();
if ($yaExiste) continue;
```

**⚠ DECISIÓN DE NEGOCIO PENDIENTE:** El código actual usa `where('estado','MEN')` —
hoy **excluye** a los socios INA/ANU (41 socios). El cambio a `whereNot('estado','CAN')`
los **incluiría**, empezando a cobrarles consumo mínimo. **Por defecto conservador:
mantener solo `MEN`** hasta que el club confirme. Marcado para confirmación.

#### `activarAnualidad()` / `desactivarAnualidad()`
Actualmente hacen `->first()` sobre `socios_membresias` → con múltiples filas devuelve
una arbitraria. Solución definitiva en Fase H/4.6 (por `id`). **Parche temporal:**
identificar la fila por `clave_membresia` usando `clave_mem_f` de la anualidad:
```php
$socio_membresia = SocioMembresia::where('id_socio', $idSocio)
    ->where('clave_membresia', $anualidad->clave_mem_f)
    ->first();
```

**Riesgo:** Medio — facturación masiva.

---

### Fase F — Acceso, UI de Recepción y consumidores de estado

**Archivos:**

| Archivo | Cambio |
|---|---|
| `Livewire/Acceso/Socios/Principal.php` | Eager load `socioMembresias.membresia` |
| `acceso/socios/principal.blade.php` | Listar todas; acceso si `socioMembresias->where('estado','!=','CAN')->isNotEmpty()` |
| `recepcion/estados/principal.blade.php` | Mostrar cada membresía con su estado individual |
| `Recepcion/Estados/CargosNuevo.php` | Cargar `socioMembresias`; lógica ANU/CAN por membresía |
| `recepcion/estados/cargos-nuevo.blade.php` | Lógica ANU/CAN por membresía individual |
| `ReportesController.php:978,990` | Usa la **relación** `$socio->socioMembresia` → cubierto por el accessor (no-null). Verificar |

#### ⚠ Subgrupo crítico: validación de venta con query directa

Estos 4 archivos **no usan la relación** — hacen `SocioMembresia::where('id_socio',X)->first()`
directo, por lo que **el accessor NO los cubre**. Todos tienen la misma lógica:

```php
$resultMembresia = SocioMembresia::where('id_socio', $socio->id)->first();
if (!$resultMembresia)                      throw 'No se encontro membresia';
else if ($resultMembresia->estado == 'CAN') throw 'Membresia cancelada';
```

**Bug con múltiples membresías:** `->first()` devuelve una fila **arbitraria** (menor id).
Un socio con una membresía CAN y otra MEN podría ser **bloqueado de comprar** aunque
tenga una activa. **La lógica debe cambiar** de "la membresía no es CAN" a "existe al
menos una membresía no-CAN":

```php
$tieneActiva = SocioMembresia::where('id_socio', $socio->id)
    ->whereNot('estado', 'CAN')->exists();
$tieneAlguna = SocioMembresia::where('id_socio', $socio->id)->exists();
if (!$tieneAlguna)      throw 'No se encontro membresia';
else if (!$tieneActiva) throw 'Membresia cancelada';
```

| Archivo | Módulo |
|---|---|
| `Forms/VentaForm.php:209` (`setSocioPago`) | Recepción ventas |
| `Recepcion/Ventas/Nueva/SearchBar.php:26` | Recepción ventas |
| `Recepcion/Ventas/Nueva/PagosModalBody.php:109` | Recepción ventas |
| `Puntos/Ventas/Nueva/Container.php:65` | **POS paralelo** ⚠ |

> **Corrección a "POS sin cambios":** el POS de **socios** (`Puntos/Socios/Container.php`
> + su vista) sí queda cubierto por el accessor. Pero el POS de **ventas**
> (`Puntos/Ventas/Nueva/Container.php:65`) usa query directa y tiene este bug — **sí
> requiere el cambio de lógica.** Coordinar con el equipo del POS paralelo.

**Riesgo:** Medio-Alto — afecta el flujo de ventas en recepción Y en el POS paralelo.

---

### Fase G — Exports

**Archivos:**

#### `SociosExport.php` (⚠ fallo funcional, no menor)
Hace `->join('socios_membresias',...)`. Con múltiples filas por socio, el reporte
**duplicaría cada socio** (una fila por membresía). Corregir: agrupar por socio y
concatenar membresías (GROUP_CONCAT) o construir desde el modelo con `socioMembresias`.

#### `RecibosExport.php:195`
Fallback `SocioMembresia::where('id_socio',...)->first()` → fila arbitraria. Usar
accessor de compatibilidad o la membresía más relevante.

#### `CarteraVencidaExport.php`
`todasCanceladas()` ya itera todas las membresías del socio. Verificar que lee
`socioMembresias` (plural) tras el cambio. Lógica sin cambio.

**Riesgo:** Bajo–Medio (SociosExport requiere cuidado).

---

### Fase H — Fases 4.5 y 4.6 (simplificadas por el cambio)

#### Fase 4.5A — Dropdown anualidad
`Anualidad/Nueva.php`: `Membresias::all()` → `Membresias::where('disponible', true)->get()`
**con excepción:** si el socio ya tiene una membresía `disponible=false`, incluirla
(igual que hace `SociosEditar`).

#### Fase 4.5B — Consumo mínimo
Resuelto en Fase E (`cargarDiferencias` con `MAX`).

#### Fase 4.6 — Anualidad por membresía individual
- Nueva migración: agregar `id_socios_membresia` (FK nullable) a `anualidades`.
- `Anualidad/Nueva.php`: selector que muestra `socioMembresias` activas del socio.
- `activarAnualidad()`: actualizar la fila por `id` exacto + sincronizar su `socios_cuotas`
  a la cuota ANU.
- `desactivarAnualidad()`: restaurar esa fila por `id` + su `socios_cuotas`.

---

## Orden de ejecución recomendado

```
A → B → D → C → E → F → G → H
```
(D antes que C para que el observer no interfiera al editar socios durante pruebas.)

---

## Archivos afectados (resumen)

| Fase | Archivo | Tipo |
|---|---|---|
| A | `migrations/...backfill_adicionales_into_socios_membresias.php` (nuevo) | Datos |
| B | `app/Models/Socio.php` | Relaciones |
| C | `app/Livewire/Forms/SocioForm.php` | Lógica |
| D | `app/Observers/SocioCuotaObserver.php` + `AppServiceProvider` | Simplificación |
| E | `app/Http/Controllers/CargosController.php` | Facturación |
| F | `app/Livewire/Acceso/Socios/Principal.php` | UI |
| F | `resources/views/livewire/acceso/socios/principal.blade.php` | Vista |
| F | `resources/views/livewire/recepcion/estados/principal.blade.php` | Vista |
| F | `app/Livewire/Recepcion/Estados/CargosNuevo.php` | UI |
| F | `resources/views/livewire/recepcion/estados/cargos-nuevo.blade.php` | Vista |
| F | `app/Http/Controllers/ReportesController.php` | Verificar |
| F | `app/Livewire/Recepcion/Ventas/Nueva/SearchBar.php` | Ventas (lógica CAN) |
| F | `app/Livewire/Recepcion/Ventas/Nueva/PagosModalBody.php` | Ventas (lógica CAN) |
| F | `app/Livewire/Forms/VentaForm.php` | Ventas (lógica CAN) |
| F | `app/Livewire/Puntos/Ventas/Nueva/Container.php` | **POS — Ventas (lógica CAN)** ⚠ |
| G | `app/Exports/SociosExport.php` | Reporte |
| G | `app/Exports/RecibosExport.php` | Reporte |
| G | `app/Exports/CarteraVencidaExport.php` | Reporte |
| H | `app/Livewire/Sistemas/Recepcion/Anualidad/Nueva.php` | 4.5A / 4.6 |
| H | `app/Http/Controllers/CargosController.php` | 4.6 |
| H | `migrations/...add_id_socios_membresia_to_anualidades.php` (nuevo) | 4.6 |

**Total: 21 archivos** (3 nuevos, 18 modificados)

**Sin cambios (compatibilidad por accessor):** POS de **socios** —
`Puntos/Socios/Container.php` y `puntos/socios/container.blade.php` (usan la relación
`$socio->socioMembresia`, cubiertos por el accessor).

**⚠ POS de ventas SÍ cambia:** `Puntos/Ventas/Nueva/Container.php:65` usa query directa
con validación CAN → requiere el cambio de lógica (ver subgrupo crítico en Fase F).

**Funciona por `id`, revisar formato:** `SociosMembresiasImport.php`,
`ExcelController.php:46`, `registros.blade.php:23` — el import de Excel "RELACION DE
MEMBRESIAS" opera por `id` (no crashea), pero el archivo Excel ahora contiene múltiples
filas por socio. Documentar el nuevo formato a quien lo use.

---

## Decisiones

1. **Consumo mínimo a INA/ANU:** ¿los socios con membresía inactiva o anual deben
   recibir cargo por consumo mínimo? _Pendiente — no bloquea._ Default seguro: mantener
   el comportamiento actual (solo MEN). Se necesita antes de **cerrar la Fase E**; si
   no hay respuesta, se queda el default.
2. **Deseleccionar vs marcar CAN:** ✅ **Resuelto.** "Cancelada" es la única vía para
   dar de baja una membresía guardada; "Seleccionar" solo revierte errores de captura
   de membresías no guardadas (ver Fase C).
3. **Coordinación con POS paralelo:** _Pendiente — no bloquea el inicio._ No es decisión
   de código sino de comunicación: avisar al equipo del POS **antes del merge** de
   `Puntos/Ventas/Nueva/Container.php` (Fase F) para evitar conflictos.
4. **Cancelación total — ¿cuántas CAN conservar?:** ✅ **Resuelto: solo una** (la de
   mayor monto), preservando el comportamiento actual. Las cancelaciones parciales no
   dejan rastro individual (ver Fase C).

---

## Riesgos

| Riesgo | Prob. | Impacto | Mitigación |
|---|---|---|---|
| Desincronización `socios_membresias.estado` ↔ `socios_cuotas` cuota->tipo | Media | Alto | Invariante documentada; SocioForm escribe ambas en una transacción |
| Bug en `SocioForm::update()` rompe edición | Media | Alto | Pruebas exhaustivas con datos reales |
| `cargarMensualidades`/`cargarRecargos` cobran doble sin DISTINCT | Alta si se olvida | Alto | Fix simple y verificable en Fase E |
| `SociosExport` duplica socios por el join | Alta si se olvida | Medio | Agrupar/GROUP_CONCAT en Fase G |
| Accessor devuelve membresía no esperada (menor id) | Baja | Bajo | Consumidores solo usan estado; documentado |
| `activarAnualidad` toca fila incorrecta antes de 4.6 | Media | Medio | Parche por `clave_mem_f` en Fase E |
| `cargarDiferencias` duplica cargos (sin idempotencia) | Alta si se omite | Alto | Idempotencia marcada OBLIGATORIA en Fase E |
| Venta bloqueada a socio con una CAN y otra activa (`->first()` arbitrario) | Alta si se omite | Alto | Cambiar lógica a "existe alguna no-CAN" en los 4 archivos de ventas |
| Backfill inserta estado inválido (`EST`) en `socios_membresias` | Baja (verificado) | Medio | Filtro `c.tipo IN ('MEN','INA','ANU')` en Fase A |
| `update()` deja al socio sin filas al cancelar todo (caso total omitido) | Media | Alto | Pseudocódigo Fase C incluye explícitamente el caso A (cancelación total) |

---

## Verificación / pruebas antes de producción

1. Backfill (Fase A) en BD de prueba → cada membresía de `socios_cuotas` tiene su fila en `socios_membresias`.
2. Registrar socio con 2 membresías → 2 filas en `socios_membresias` y 2 en `socios_cuotas`, estados coinciden.
3. Editar socio: cambiar estado de una membresía (MEN→INA) → ambas tablas reflejan INA.
4. Editar socio: quitar una membresía → se elimina de ambas tablas (o queda CAN si aplica).
5. `cargarMensualidades` con socio de múltiples membresías → un solo procesamiento, sin duplicados.
6. `cargarDiferencias` dos veces el mismo mes → cero duplicados (idempotencia).
7. Pórtico de acceso: socio con una CAN y una MEN → ACCESO PERMITIDO.
8. Pórtico: socio con todas CAN → ACCESO DENEGADO, sin crash (accessor no-null).
9. POS: cargar socio con múltiples membresías → muestra estado sin crash.
10. `SociosExport` → cada socio aparece una sola vez.
