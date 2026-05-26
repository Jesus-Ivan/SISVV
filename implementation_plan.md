Proyecto De Mejora: Sistema De Cuotas Personalizadas Para El Registro De Socios En Vista Verde Country Club.

OBJETIVO GENERAL

Permitir que el sistema SISVV soporte la asignación de precios personalizados (descuentos o cuotas especiales) a los socios en sus mensualidades y cargos fijos, así como el manejo de múltiples membresías simultáneas por socio, optimizando el proceso de facturación mensual masiva.

1. ALINEACIÓN CON LOS REQUERIMIENTOS FUNCIONALES
A continuación se detalla cómo cada requerimiento funcional (RF) del documento de requisitos será cubierto por esta propuesta:

    RF 1 – Múltiples membresías simultáneas y compatibilidad:
    - Las membresías adicionales del socio se registrarán en la tabla "socios_cuotas" vinculando al socio con la cuota correspondiente de tipo membresía (MEN).
    - Para mantener la compatibilidad con el control de acceso e históricos, la tabla legacy "socios_membresias" registrará la membresía principal.
    - Se modificará la relación `socioMembresia` en el modelo Socio a `socioMembresias` (de HasOne a HasMany) para soportar todas las membresías contratadas del socio de forma concurrente.

    RF 2.1 – Mínimo una membresía obligatoria:
    Se implementará una validación en el formulario de registro y edición de socios (SocioForm) que impida guardar un socio sin al menos una membresía seleccionada.

    RF 2.2 – Cuotas base definidas:
    Las membresías y cargos fijos mantienen su precio base en la tabla "cuotas" (columna "monto"). Este precio aplica por defecto para todos los socios.

    RF 2.3 – Cuotas personalizadas ilimitadas:
    Se agregará una nueva columna llamada "monto_personalizado" en la tabla "socios_cuotas". Un socio puede tener precios especiales en cualquier cantidad de cuotas contratadas.

    RF 2.4 – Independencia entre socios:
    El precio personalizado se almacena exclusivamente en la relación individual del socio con su cuota. Modificar el precio especial de un socio no altera el precio de ningún otro socio ni el catálogo base del club.

    RF 2.5 – Prevención de cobros duplicados por periodo:
    El proceso de facturación mensual iterará de manera estricta por cada una de las cuotas activas asignadas al socio (`SocioCuota`). Para cada cuota, consultará si ya existe un registro en `EstadoCuenta` para el mes y año del proceso (`whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)->where('id_cuota', $cuota->id_cuota)`). Si existe, el cargo se omite; si no, se factura. Esto resuelve el bug del ciclo for y reset() del código original, evitando cobros duplicados accidentales entre cuotas del mismo tipo.

    RF 2.6 – Impedir membresías idénticas:
    - Se aplicará una restricción de unicidad a nivel de base de datos (índice único compuesto en las columnas "id_socio" e "id_cuota" de la tabla "socios_cuotas").
    - Se añadirá una regla de validación amigable en el componente Livewire del panel de Sistemas para alertar al usuario antes de provocar una excepción SQL.

    RF 3 – Configuración de Cuotas y Membresías (Roles y Permisos):
    - **Departamento de Recepción (Alta/Edición de Socios):** El personal de Recepción asigna/edita los datos del socio y su membresía principal o cargos iniciales desde `SociosNuevo` y `SociosEditar`. Recepción **NO** tiene permisos de edición sobre los montos personalizados.
    - **Departamento de Sistemas (Edición de Cuotas y Precios Especiales):** Los usuarios de Sistemas configurarán y activarán cuotas adicionales (membresías secundarias, lockers, etc.) y asignarán el precio especial (`monto_personalizado`) desde el listado de socios de Sistemas (`livewire/sistemas/recepcion/socios/lista-socios.blade.php`) mediante la acción dedicada "Editar Cuotas" que opera bajo el middleware `sistemas`.

    RF 4 – Registro en estado de cuenta:
    El proceso de facturación utilizará el precio personalizado del socio (si existe en `socios_cuotas`) como prioridad. De no existir, aplicará la tarifa base de la cuota. Los cargos generados reflejarán esta lógica en el estado de cuenta.

    RF 5 – Reportes actualizados:
    Las exportaciones a Excel de socios (SociosExport) y cartera vencida (CarteraVencidaExport) se modificarán para listar todas las membresías contratadas por cada socio. Asimismo, `SociosExport` calculará los montos totales de cobro utilizando el precio personalizado (`monto_a_cobrar`) del socio en lugar de usar siempre la tarifa base.

    RF 6 – Visualización clara en Búsqueda y Escaneo (Pórtico y POS):
    Se actualizarán las vistas del Pórtico de Acceso (`livewire/acceso/socios/principal.blade.php`) y la pantalla del socio en el Punto de Venta (`livewire/puntos/socios/container.blade.php`) para listar todas las membresías activas asociadas al socio. El acceso al club se otorgará si al menos una de las membresías contratadas está en estado activo (no cancelada).

    RF 7 – Actualización masiva de cuotas base desde Sistemas:
    Este diseño lo cumple de forma nativa. Al modificar el precio de una cuota base en la tabla "cuotas" desde el catálogo de Sistemas, el cambio aplica automáticamente para todos los socios asociados a ella en el siguiente ciclo de facturación (excepto para los que tengan un precio personalizado fijado).

    RF 8 – Diferenciación visual de socios con cuotas personalizadas:
    - **En la interfaz (Estados de Cuenta):** En el apartado de **Estados de Cuenta** de Recepción (`livewire/recepcion/estados/principal.blade.php`) se mostrará una insignia o distintivo visual (badge) junto al nombre del socio si cuenta con al menos una cuota con monto personalizado. Adicionalmente, se agregará un filtro en la barra de búsqueda de esta misma pantalla para listar únicamente a los socios que tienen tarifas especiales.
    - **En reportes:** En el reporte de Excel (`SociosExport`), se añadirá una columna titulada "TARIFA PERSONALIZADA" que mostrará "SÍ" si el socio posee alguna cuota personalizada o "NO" si solo paga tarifas base.

2. PLAN DE IMPLEMENTACIÓN POR FASES

FASE 1: Migración Estructural de Base de Datos

1.1. Alteración de la tabla "socios_cuotas":
      - Cambiar el tipo de las columnas "id_socio" e "id_cuota" a entero sin signo (unsigned) para homologarlos con las tablas "socios" y "cuotas".
      - Agregar la columna "monto_personalizado" de tipo decimal (10,2), con valor nulo por defecto.
      - Crear un índice único compuesto en las columnas "id_socio" e "id_cuota" para impedir duplicados.
      - Crear llaves foráneas formales apuntando a las tablas "socios" y "cuotas".

1.2. Corrección menor en la tabla "cuotas":
      - Ajustar el método de reversión (rollback) de la migración original para que apunte al nombre correcto de la tabla ("cuotas" en lugar de "cuotas_club").

FASE 2: Actualización de la Lógica Interna (Modelos de Laravel)

2.1. Modelo SocioCuota:
      - Habilitar la columna "monto_personalizado" para asignación masiva.
      - Crear una propiedad calculada llamada "monto_a_cobrar" que encapsula la regla de negocio: si el socio tiene un precio personalizado, devuelve ese valor; de lo contrario, devuelve el precio base del catálogo.

2.2. Modelo Socio (Relaciones de Membresías):
      - Convertir la relación `socioMembresia` a `socioMembresias` (HasMany) y definir la relación con `socios_cuotas` filtrando por tipo de cuota `MEN` para poder recuperar todas las membresías contratadas del socio fácilmente.

FASE 3: Módulos de Acceso, Recepción y Reportes

3.1. Validación del Formulario de Socios (Recepción):
      - Implementar validación en `SocioForm` para asegurar que al menos una membresía permanezca seleccionada.
      - Ajustar la lógica del método `comprobar` para deshabilitar el registro de integrantes familiares únicamente si todas las membresías contratadas son de tipo "INDIVIDUAL".

3.2. Pantalla de Acceso Pórtico y Punto de Venta (RF 6):
      - Modificar `livewire/acceso/socios/principal.blade.php` y `livewire/puntos/socios/container.blade.php` para iterar y mostrar en una lista clara todas las membresías asignadas al socio en vez de solo una.
      - Actualizar la lógica del controlador para permitir el acceso siempre que al menos una de las membresías del socio no esté en estado 'CAN'.

3.3. Interfaz de Estados de Cuenta (Recepción - RF 8):
      - Modificar la vista principal de estados de cuenta (`livewire/recepcion/estados/principal.blade.php`) y su controlador (`Principal.php`).
      - Agregar una insignia o badge visual junto al nombre del socio si tiene cuotas personalizadas (`monto_personalizado IS NOT NULL` en `socios_cuotas`).
      - Integrar un filtro tipo toggle/select en la barra de búsqueda superior para mostrar solo socios con tarifas especiales.

3.4. Actualización de reportes y exportaciones:
      - Modificar `SociosExport.php` para calcular los totales en la función `sumar_cuotas` usando la propiedad `monto_a_cobrar` de `SocioCuota` en lugar del monto fijo de catálogo. Añadir la columna **"TARIFA PERSONALIZADA"** (indicando "SÍ" o "NO" e indicando qué cuotas están personalizadas).
      - Actualizar `CarteraVencidaExport.php` para que liste todas las membresías contratadas separadas por comas, y ajustar la consulta para comprobar que el socio no tenga todas sus membresías canceladas antes de filtrarlo.

FASE 4: Módulo de Sistemas y Facturación Masiva

4.1. Interfaz de Edición de Precios por Socio (Sistemas - Lista de Socios):
      - En la vista de Sistemas (`livewire/sistemas/recepcion/socios/lista-socios.blade.php`), agregar un botón de acción "Editar Cuotas" en la fila de cada socio.
      - Crear un modal que permita activar/desactivar membresías y cargos fijos en la tabla `socios_cuotas` y capturar el "Monto Personalizado" exclusivo.
      - Implementar validación amigable en el modal para impedir que se asigne la misma membresía o cuota dos veces.
      - Mostrar el costo base de catálogo como placeholder cuando el campo de monto personalizado esté vacío.

4.2. Optimización y Rediseño de la Facturación Masiva (RF 2.5):
      - Refactorizar la función "cargarMensualidades" del controlador de cargos [CargosController] para eliminar la comparación por conteos generales y agrupados.
      - La nueva lógica iterará por cada socio y, a su vez, por cada una de sus `socios_cuotas` contratadas. Para cada cuota individual, se verificará en `EstadoCuenta` si existe un cargo en el mismo mes y año que coincida con el `id_cuota` específico de la fila. Si no existe, se insertará el cargo utilizando la propiedad `monto_a_cobrar`.

4.3. Funcionamiento de la actualización masiva de cuotas base desde Sistemas (Requerimiento 7):
      - El sistema ya cuenta con la pantalla de administración de cuotas de Sistemas ("App/Livewire/Sistemas/Recepcion/Cuotas.php").
      - Al modificar el precio de una cuota en esta pantalla, el cambio se guardará en la tabla "cuotas".
      - Dado que los precios base no se replican en cada socio, la actualización del catálogo base aplica automáticamente a los socios vinculados que pagan la tarifa regular.

VERIFICACIÓN Y PRUEBAS

Prueba 1 – Asignación de membresías en Recepción (Alta/Edición):
Crear o editar un socio desde el módulo de Recepción. Verificar que se registre su membresía principal y datos personales sin errores.

Prueba 2 – Configuración de cuotas personalizadas desde la Lista de Socios (Sistemas):
- Ingresar a Sistemas -> Recepción -> Lista de Socios -> "Editar Cuotas".
- Asignar un precio especial a una membresía y activar la cuota de Locker con su precio base. Guardar y verificar que la base de datos se actualice correctamente.
- Intentar asignar la misma cuota/membresía de nuevo y verificar que el formulario muestre un error de validación claro en lugar de provocar un error SQL en pantalla.

Prueba 3 – Actualización Masiva de Tarifa Base y Cobro Mixto:
- Modificar el precio de una cuota base desde el catálogo de Sistemas.
- Ejecutar el proceso de facturación mensual para el socio con precio personalizado de la Prueba 2. Verificar que a los socios que pagan la tarifa base se les aplique el nuevo precio, mientras que el socio modificado mantenga su precio especial intacto en su estado de cuenta.

Prueba 4 – Rediseño del Ciclo de Cobro contra duplicados y preservación de adeudos históricos:
- Asignar al socio dos membresías del mismo tipo (ej. dos cuotas de membresía activa).
- Generar manualmente un cargo para una de las cuotas en el mes corriente en `EstadoCuenta`.
- Ejecutar el proceso de facturación masiva. Validar que la membresía que ya tenía cargo no sea facturada nuevamente (cero cobros duplicados) y que la segunda membresía sí sea facturada de forma correcta.
- Verificar que los saldos pendientes del mes anterior permanezcan completamente intactos, sin ser eliminados ni modificados por la nueva facturación.

Prueba 5 – Identificación visual y listado claro (RF 6 y RF 8):
- Escanear o buscar el número de socio en el Pórtico y en la pantalla del Punto de Venta. Confirmar que se listen todas las membresías contratadas con su respectivo estado.
- Acceder al listado de Estados de Cuenta en recepción y activar el filtro "Solo con Tarifa Especial". Verificar que únicamente se muestren los socios con montos personalizados y con su insignia visual.
- Exportar la lista de socios a Excel y validar que la columna "TARIFA PERSONALIZADA" registre correctamente el valor "SÍ" o "NO" y que la suma de cuotas refleje los precios personalizados cobrados.

4. PLAN DE EMERGENCIA (ROLLBACK)

En caso de detectarse una falla crítica después del despliegue a producción:

    Paso 1: Revertir el código del sistema a la versión anterior estable mediante el sistema de control de versiones (Git).

    Paso 2: La columna "monto_personalizado" permanecerá en la base de datos pero será ignorada por el código de la versión anterior, permitiendo que el club continúe operando con las tarifas base normales sin interrupción alguna en el servicio.
