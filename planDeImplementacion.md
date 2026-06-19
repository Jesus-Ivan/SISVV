Proyecto De Mejora: Sistema De Cuotas Personalizadas Para El Registro De Socios En Vista Verde Country Club.

OBJETIVO GENERAL

Permitir que el sistema SISVV soporte la asignación de precios personalizados (descuentos o cuotas especiales) a los socios en sus mensualidades y cargos fijos, así como el manejo de múltiples membresías simultáneas por socio, optimizando el proceso de facturación mensual masiva.

ALINEACIÓN CON LOS REQUERIMIENTOS FUNCIONALES
A continuación se detalla cómo cada requerimiento funcional (RF) del documento de requisitos será cubierto por esta propuesta:


RF 1 – Múltiples membresías simultáneas:
  Se registrarán como múltiples filas en la tabla "socios_cuotas", donde cada fila vincula al socio con una cuota de tipo membresía (MEN).

  RF 2.1 – Mínimo una membresía obligatoria:
  Se implementará una validación en el formulario de registro y edición de socios (SocioForm) que impida guardar un socio sin al menos una membresía seleccionada.

  RF 2.2 – Cuotas base definidas:
  Las membresías y cargos fijos mantienen su precio base en la tabla "cuotas" (columna "monto"). Este precio aplica por defecto para todos los socios.
  
  RF 2.3 – Cuotas personalizadas ilimitadas:
  Se agregará una nueva columna llamada "monto_personalizado" en la tabla "socios_cuotas". Un socio puede tener precios especiales en cualquier cantidad de cuotas contratadas.
  
  RF 2.4 – Independencia entre socios:
  El precio personalizado se almacena exclusivamente en la relación individual del socio con su cuota. Modificar el precio especial de un socio no altera el precio de ningún otro socio ni el catálogo base del club.
  





RF 2.5 – Prevención de cobros duplicados por periodo:
    El proceso de facturación masiva compara las cuotas contratadas del socio contra los cargos ya registrados en su estado de cuenta para el mes en curso. Si el cargo ya existe, el sistema lo omite automáticamente.
    Solución para evitar duplicados y preservar adeudos:El sistema consultará el estado de cuenta filtrando de manera estricta por el mes y año del proceso de facturación ejecutado (`whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)`). De esta forma, si el socio tiene un adeudo del mes anterior con el mismo ID de cuota (Y CARGO), dicho registro histórico no afectará la facturación del nuevo mes, ni el sistema eliminará o modificará los registros históricos de adeudos.  
(OBSERVACIONES: Hacer que diferencie entre adeudos, por ejemplo si el socio tiene 2 casilleros, uno con precio especial y el otro normal)
  RF 2.6 – Impedir membresías idénticas:
  Se aplicará una restricción de unicidad a nivel de base de datos (índice único compuesto en las columnas "id_socio" e "id_cuota" de la tabla "socios_cuotas"). Esto hace estructuralmente imposible que un socio tenga la misma membresía registrada dos veces, incluso ante un error humano.
  
    RF 3 – Configuración de Cuotas y Membresías:
    Para mantener la simplicidad en el registro diario, en los formularios de edición y creación de socios del módulo de Recepción (`SociosNuevo` y `SociosEditar`), únicamente se reemplazará el selector simple por casillas de verificación (checkboxes) permitiendo asignar múltiples membresías simultáneas de forma rápida. 
    Ubicación de la edición de precios: La captura de precios especiales y la gestión avanzada de las cuotas contratadas se trasladará al apartado de Sistemas/Recepción -> Lista de Socios. Allí se agregará un botón de "Editar Cuotas" para cada socio, el cual abrirá una interfaz dedicada y exclusiva para la gestión completa de sus membresías y cuotas fijas, incluyendo la asignación de sus precios personalizados.
(OBERSERVACIONES: Unificar la tabla en el apartado de Sistemas y que se quede con el nombre “Socios Cuotas”, Ademas de que en el apartado de Nuevo/Editar Socio unicamente se halla un Checkbox para seleccionar que membresias tendra el socio)









  
  RF 4 – Registro en estado de cuenta:
  El proceso de facturación utilizará el precio personalizado del socio (si existe) como prioridad. De no existir, aplicará la tarifa base del catálogo. Los cargos generados reflejarán esta lógica en el estado de cuenta.
  
  RF 5 – Reportes actualizados:
  Las exportaciones a Excel de socios (SociosExport) y cartera vencida (CarteraVencidaExport) se modificarán para listar todas las membresías contratadas por cada socio.
  
  



RF 7 – Actualización masiva de cuotas base desde Sistemas:
  Este diseño lo cumple de forma nativa. Al modificar el precio de una cuota base en la tabla "cuotas", el cambio aplica automáticamente para todos los socios asociados a ella en el siguiente ciclo de facturación (excepto para los que tengan un precio personalizado fijado).


 RF 8 – Diferenciación visual de socios con cuotas personalizadas (Nuevo):
   -En la interfaz: En el listado de socios de recepción (`livewire/recepcion/socios.blade.php`), se mostrará una insignia distintiva junto al nombre del socio si cuenta con al menos una cuota con monto personalizado. Adicionalmente, se integrará un filtro en la barra de búsqueda para listar únicamente a los socios que tienen tarifas especiales.
    - En reportes: En el reporte de Excel (`SociosExport`), se añadirá una columna titulada "TARIFA PERSONALIZADA" que mostrará "SÍ" si el socio posee alguna cuota personalizada o "NO" si solo paga tarifas base.
(OBSERVACIONES: MOVERLO A “ESTADOS DE CUENTA” EN LUGAR DE “SOCIOS” ADEMAS DE MODIFICAR EL DISEÑO PARA QUE NO SE VEA FEO)


2. PLAN DE IMPLEMENTACIÓN POR FASES









FASE 1: Migración Estructural de Base de Datos



1.1. Alteración de la tabla "socios_cuotas":
      - Cambiar el tipo de las columnas "id_socio" e "id_cuota" a entero sin signo (unsigned) para homologarlos con las tablas "socios" y "cuotas".
      - Agregar la columna "monto_personalizado" de tipo decimal, con valor nulo por defecto.
      - Crear un índice único compuesto en las columnas "id_socio" e "id_cuota" para impedir duplicados.
      - Crear llaves foráneas formales apuntando a las tablas "socios" y "cuotas".
  



FASE 2: Actualización de la Lógica Interna (Modelos de Laravel)



2.1. Modelo SocioCuota:
      - Habilitar la columna "monto_personalizado" para asignación masiva.
      - Crear una propiedad calculada llamada "monto_a_cobrar" que encapsula la regla de negocio: si el socio tiene un precio personalizado, devuelve ese valor; de lo contrario, devuelve el precio base del catálogo.

FASE 3: Módulo de Recepción y Reportes (Interfaz de Usuario)

3.1. Formulario de registro y edición de socios (Recepción):
      - Eliminar el campo simple de membresía única (dropdown) en el formulario de Recepción (`SociosNuevo` y `SociosEditar`).
      - Implementar una lista de casillas de verificación (checkboxes) para permitir la selección rápida de múltiples membresías simultáneas.
      - Implementar validaciones en `SocioForm` para asegurar que al menos una membresía (tipo 'MEN') permanezca seleccionada.

3.2. Listado de socios (Interfaz de Recepción):
      - Modificar el controlador `Socios.php` para cargar la relación `sociosCuotas` filtrando aquellos que tengan `monto_personalizado IS NOT NULL`.
      - Agregar una insignia o distintivo visual (badge) en la vista `socios.blade.php`.
      - Incorporar un control de búsqueda para filtrar la lista mostrando únicamente socios con tarifas personalizadas.

3.3. Actualización de reportes y exportaciones:
      - Modificar `SociosExport.php` para listar todas las cuotas contratadas por socio en las columnas correspondientes y agregar la nueva columna "TARIFA PERSONALIZADA" (indicando "SÍ" o "NO" y detallando qué cuotas están personalizadas).
      - Actualizar `CarteraVencidaExport.php` para que liste todas las membresías contratadas separadas por comas.

FASE 4: Módulo de Sistemas y Cuotas Masivas

4.1. Interfaz de Edición de Precios por Socio (Sistemas/Recepcion/ListaSocios):
      - En la vista del listado de socios de Sistemas, agregar un botón de acción "Editar Cuotas" en la fila de cada socio.
      - Crear un nuevo componente o modal dedicado donde se listen todas las cuotas disponibles (membresías y cargos fijos).
      - Para cada cuota, incluir una casilla (checkbox) para activarla/desactivarla al socio y un campo de entrada numérico contiguo para capturar el "Monto Personalizado" exclusivo.
      - Mostrar el costo base de catálogo como placeholder cuando el campo personalizado esté vacío.

4.2. Funcionamiento de la actualización masiva de cuotas base (Requerimiento 7):
      - El sistema ya cuenta con la pantalla de administración de cuotas de Sistemas ("App/Livewire/Sistemas/Recepcion/Cuotas.php"). 
      - Con el nuevo diseño, cuando el departamento de Sistemas edite el precio de una cuota en esta pantalla el cambio se guardará en la tabla "cuotas".
      - Al no requerir replicar el precio base en cada socio, la actualización es inmediata y masiva para el 100% de los socios vinculados que no tengan un precio personalizado.

4.3. Optimización del proceso de facturación mensual:
      - Refactorizar la función "cargarMensualidades" del controlador de cargos para que itere por socio único (agrupando por número de socio) en lugar de iterar por membresía. Esto elimina la redundancia cuando un socio tiene múltiples membresías.
      - Asegurar que la consulta a `EstadoCuenta` para verificar cobros existentes se realice usando de forma rigurosa la cláusula `whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)`. Esto evitará que adeudos pendientes del mes previo interfieran en la facturación del mes corriente.

4.4. Aplicación de precios personalizados:
      - Reemplazar la referencia directa al precio base del catálogo por la nueva propiedad calculada "monto_a_cobrar", que resolverá automáticamente si debe cobrar el precio especial o el precio base.
Proyecto De Mejora: Sistema De Cuotas Personalizadas Para El Registro De Socios En Vista Verde Country Club.

OBJETIVO GENERAL

Permitir que el sistema SISVV soporte la asignación de precios personalizados (descuentos o cuotas especiales) a los socios en sus mensualidades y cargos fijos, así como el manejo de múltiples membresías simultáneas por socio, optimizando el proceso de facturación mensual masiva.

1. ALINEACIÓN CON LOS REQUERIMIENTOS FUNCIONALES
A continuación se detalla cómo cada requerimiento funcional (RF) del documento de requisitos será cubierto por esta propuesta:

    RF 1 – Múltiples membresías simultáneas:
    Se registrarán como múltiples filas en la tabla "socios_cuotas", donde cada fila vincula al socio con una cuota de tipo membresía (MEN).

    RF 2.1 – Mínimo una membresía obligatoria:
    Se implementará una validación en el formulario de registro y edición de socios (SocioForm) que impida guardar un socio sin al menos una membresía seleccionada.

    RF 2.2 – Cuotas base definidas:
    Las membresías y cargos fijos mantienen su precio base en la tabla "cuotas" (columna "monto"). Este precio aplica por defecto para todos los socios.

    RF 2.3 – Cuotas personalizadas ilimitadas:
    Se agregará una nueva columna llamada "monto_personalizado" en la tabla "socios_cuotas". Un socio puede tener precios especiales en cualquier cantidad de cuotas contratadas.

    RF 2.4 – Independencia entre socios:
    El precio personalizado se almacena exclusivamente en la relación individual del socio con su cuota. Modificar el precio especial de un socio no altera el precio de ningún otro socio ni el catálogo base del club.

    RF 2.5 – Prevención de cobros duplicados por periodo:
    El proceso de facturación masiva compara las cuotas contratadas del socio contra los cargos ya registrados en su estado de cuenta para el mes en curso. Si el cargo ya existe, el sistema lo omite automáticamente.
    *Solución para evitar duplicados y preservar adeudos:* El sistema consultará el estado de cuenta filtrando de manera estricta por el mes y año del proceso de facturación ejecutado (`whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)`). De esta forma, si el socio tiene un adeudo del mes anterior con el mismo ID de cuota (y cargo), dicho registro histórico no afectará la facturación del nuevo mes, ni el sistema eliminará o modificará los registros históricos de adeudos.

    RF 2.6 – Impedir membresías idénticas:
    Se aplicará una restricción de unicidad a nivel de base de datos (índice único compuesto en las columnas "id_socio" e "id_cuota" de la tabla "socios_cuotas"). Esto hace estructuralmente imposible que un socio tenga la misma membresía registrada dos veces, incluso ante un error humano.

    RF 3 – Configuración de Cuotas y Membresías:
    - **Registro y Edición de Socios (Recepción):** Se mantendrán formularios simples y limpios para el personal de Recepción (`SociosNuevo` y `SociosEditar`). Se eliminará el campo dropdown de membresía única y se integrará únicamente un listado de casillas de verificación (checkboxes) para asignar múltiples membresías de forma rápida.
    - **Edición de Precios de Cuotas (Recepción - Lista de Socios):** La captura de precios especiales (monto_personalizado) y cargos fijos asociados se realizará en la sección de Recepción en el módulo de **Lista de Socios** (`livewire/recepcion/socios.blade.php`). En esta vista, se agregará un botón de "Editar Cuotas" para cada socio que abrirá un modal o tabla de edición para configurar los precios individuales y activar o desactivar cargos fijos (Lockers, Resguardos, etc.).

    RF 4 – Registro en estado de cuenta:
    El proceso de facturación utilizará el precio personalizado del socio (si existe) como prioridad. De no existir, aplicará la tarifa base del catálogo. Los cargos generados reflejarán esta lógica en el estado de cuenta.

    RF 5 – Reportes actualizados:
    Las exportaciones a Excel de socios (SociosExport) y cartera vencida (CarteraVencidaExport) se modificarán para listar todas las membresías contratadas por cada socio.

    RF 7 – Actualización masiva de cuotas base desde Sistemas:
    Este diseño lo cumple de forma nativa. Al modificar el precio de una cuota base en la tabla "cuotas" desde el catálogo de Sistemas, el cambio aplica automáticamente para todos los socios asociados a ella en el siguiente ciclo de facturación (excepto para los que tengan un precio personalizado fijado).

    RF 8 – Diferenciación visual de socios con cuotas personalizadas (Nuevo):
    - **En la interfaz (Estados de Cuenta):** Se moverá esta funcionalidad de la lista de socios general al apartado de **Estados de Cuenta** (`livewire/recepcion/estados/principal.blade.php`). Aquí se mostrará una insignia o distintivo visual (badge) junto al nombre del socio si cuenta con al menos una cuota con monto personalizado. Adicionalmente, se agregará un filtro en la barra de búsqueda de esta misma pantalla para listar únicamente a los socios que tienen tarifas especiales.
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

FASE 3: Módulo de Recepción y Reportes (Interfaz de Usuario)

3.1. Formulario de registro y edición de socios (Recepción):
      - Eliminar el campo simple de membresía única (dropdown) en el formulario de Recepción (`SociosNuevo` y `SociosEditar`).
      - Implementar una lista de casillas de verificación (checkboxes) para permitir la selección rápida de múltiples membresías simultáneas.
      - Implementar validaciones en `SocioForm` para asegurar que al menos una membresía (tipo 'MEN') permanezca seleccionada.

3.2. Listado de socios e Interfaz de Precios Personalizados (Recepción - Lista de Socios):
      - En la vista `livewire/recepcion/socios.blade.php`, agregar un botón de acción "Editar Cuotas" para cada socio.
      - Crear un modal o una vista dedicada en Recepción que permita gestionar las cuotas del socio: listar todas las disponibles, poder activarlas/desactivarlas con checkboxes, y editar contiguamente el campo "Monto Personalizado".
      - Mostrar el monto base del catálogo como placeholder o texto descriptivo en caso de no haber fijado una tarifa especial.

3.3. Interfaz de Estados de Cuenta (Recepción - RF 8):
      - Modificar la vista principal de estados de cuenta (`livewire/recepcion/estados/principal.blade.php`) y su controlador (`Principal.php`).
      - Agregar una insignia o badge visual junto al nombre del socio si tiene cuotas personalizadas (`monto_personalizado IS NOT NULL`).
      - Integrar un filtro tipo toggle/select en la barra de búsqueda superior para mostrar solo socios con tarifas especiales.

3.4. Actualización de reportes y exportaciones:
      - Modificar `SociosExport.php` para listar todas las cuotas contratadas por socio en las columnas correspondientes y agregar la nueva columna **"TARIFA PERSONALIZADA"** (indicando "SÍ" o "NO" y detallando qué cuotas están personalizadas).
      - Actualizar `CarteraVencidaExport.php` para que liste todas las membresías contratadas separadas por comas.

FASE 4: Módulo de Sistemas y Cuotas Masivas

4.1. Funcionamiento de la actualización masiva de cuotas base desde Sistemas (Requerimiento 7):
      - El sistema ya cuenta con la pantalla de administración de cuotas de Sistemas ("App/Livewire/Sistemas/Recepcion/Cuotas.php").
      - Al modificar el precio de una cuota en esta pantalla, el cambio se guardará en la tabla "cuotas".
      - Dado que los precios base no se replican en cada socio, la actualización del catálogo base aplica automáticamente e inmediatamente al 100% de los socios vinculados que pagan la tarifa regular.

4.2. Optimización del proceso de facturación masiva mensual:
      - Refactorizar la función "cargarMensualidades" del controlador de cargos para que itere por socio único (agrupando por número de socio) en lugar de iterar por membresía. Esto elimina la redundancia cuando un socio tiene múltiples membresías.
      - Asegurar que la consulta a `EstadoCuenta` para verificar cobros existentes se realice usando de forma rigurosa la cláusula `whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)`. Esto evitará que adeudos pendientes del mes previo interfieran en la facturación del mes corriente.

4.3. Aplicación de precios personalizados en la facturación:
      - Reemplazar la referencia directa al precio base del catálogo por la nueva propiedad calculada "monto_a_cobrar", que resolverá automáticamente si debe cobrar el precio especial o el precio base.

VERIFICACIÓN Y PRUEBAS

Prueba 1 – Asignación de múltiples membresías en Recepción:
Crear o editar un socio desde el módulo de Recepción marcando múltiples casillas de membresía. Verificar que se guarden correctamente sin errores.

Prueba 2 – Configuración de cuotas personalizadas desde la Lista de Socios (Recepción):
- Ingresar a Recepción -> Lista de Socios.
- Hacer clic en el nuevo botón "Editar Cuotas" de un socio.
- Asignar un precio especial a una membresía y activar la cuota de Locker con su precio base. Guardar y verificar que la base de datos se actualice correctamente.

Prueba 3 – Actualización Masiva de Tarifa Base y Cobro Mixto:
- Modificar el precio de una cuota base desde el catálogo de Sistemas.
- Ejecutar el proceso de facturación mensual para el socio con precio personalizado de la Prueba 2. Verificar que a los socios que pagan la tarifa base se les aplique el nuevo precio, mientras que el socio modificado mantenga su precio especial intacto en su estado de cuenta.

Prueba 4 – Protección contra cobros dobles y preservación de adeudos históricos:
- Crear una cuota en el estado de cuenta de un socio correspondiente al mes anterior y dejarla con saldo pendiente (adeudo).
- Ejecutar el proceso de facturación masiva para el mes actual.
- Verificar que se cree el cargo para el mes en curso y que el adeudo del mes anterior permanezca completamente intacto, sin ser eliminado ni modificado por la nueva facturación.

Prueba 5 – Restricción de membresías idénticas:
Intentar asignar la misma membresía dos veces al mismo socio. Verificar que el sistema muestre un error de validación y que la base de datos rechace la operación.

Prueba 6 – Identificación visual de cuotas personalizadas en Estados de Cuenta (RF 8):
- Acceder al listado de Estados de Cuenta en recepción y activar el filtro "Solo con Tarifa Especial". Verificar que únicamente se muestren los socios con montos personalizados y con su insignia visual.
- Exportar la lista de socios a Excel y validar que la columna "TARIFA PERSONALIZADA" registre correctamente el valor "SÍ" o "NO" según corresponda.

4. PLAN DE EMERGENCIA (ROLLBACK)

En caso de detectarse una falla crítica después del despliegue a producción:

    Paso 1: Revertir el código del sistema a la versión anterior estable mediante el sistema de control de versiones (Git).

    Paso 2: La columna "monto_personalizado" permanecerá en la base de datos pero será ignorada por el código de la versión anterior, permitiendo que el club continúe operando con las tarifas base normales sin interrupción alguna en el servicio.

