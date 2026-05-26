Proyecto De Mejora: Sistema De Cuotas Personalizadas Para El Registro De Socios En Vista Verde Country Club.

Requisitos Funcionales:
1.	El sistema permitirá vincular N cantidad de membresías simultáneamente, a un numero de socio.
2.	El sistema permitirá la carga masiva mensual de las cuotas vinculadas a cada socio. Respetando las reglas:
2.1.	Cada socio debe contar obligatoriamente con al menos 1 membresía. Y sus cuotas respectivas (cargos fijos). 
2.2.	Las membresías y cargos fijos tienen definidos una cuota base. 
2.3.	Un socio puede tener cualquier cantidad cuotas personalizadas.
2.4.	Las cuotas personalizadas de un socio no deben interferir con otros socios.
2.5.	El sistema debe evitar cargar las cuotas en el mes X, en caso de ya existir los registros en el mismo periodo.
2.6.	El sistema debe impedir la asignación de dos membresías idénticas para evitar errores de cobro duplicado.
3.	El sistema, mediante el departamento de “Recepción”, debe permitir la configuración de cuotas personalizadas (membresía o cargo fijos) por cada socio. 
4.	El sistema debe registrar en el estado de cuenta las cuotas personalizadas correspondientes a cada membresía activa del socio.
5.	El sistema debe generar los reportes de SOCIOS con la información de las membresías y las cuotas relacionadas.
6.	El sistema mostrara de forma clara todas las membresías del socio al momento de escanear su número o realizar la búsqueda.
7.	Mediante el departamento de “Sistemas” se debe permitir la actualización de cuotas base de forma masiva. 
Requisitos No Funcionales:
1.	El código de programación se realizará en Laravel 10 y Livewire 3.
2.	No integrar servicios en la nube para la implementación.
