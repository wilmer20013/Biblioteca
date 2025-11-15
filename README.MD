Sistema de Biblioteca

Este proyecto es una aplicación web básica para la gestión de una pequeña biblioteca. Permite la administración de libros, autores y préstamos realizados a usuarios. El sistema incluye funcionalidades de autenticación de usuarios (inicio de sesión y registro), operaciones CRUD para libros y autores, y gestión/visualización de préstamos. Está desarrollado con una arquitectura MVC (Modelo-Vista-Controlador) y utiliza una base de datos relacional normalizada.

El objetivo es proporcionar una herramienta simple y escalable para bibliotecas pequeñas, con énfasis en la mantenibilidad y la documentación completa.

Características Principales

Autenticación de Usuarios: Inicio de sesión y registro de nuevos usuarios.
Gestión de Libros: Operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para libros.

Gestión de Autores: Operaciones CRUD para autores.

Gestión de Préstamos: Registro, visualización y gestión de préstamos de libros a usuarios.

Interfaz Web: Interfaz de usuario intuitiva para todas las operaciones.

Base de Datos Relacional: Estructura normalizada para escalabilidad.

Requerimientos Funcionales

Autenticación y Registro de Usuarios:

Los usuarios deben poder registrarse proporcionando nombre, correo electrónico y contraseña.
Los usuarios registrados deben poder iniciar sesión con correo y contraseña.
Validación de datos: Correo único, contraseña segura (mínimo 8 caracteres, con mayúsculas, minúsculas y números).
Sesiones persistentes para mantener el estado del usuario logueado.

Gestión de Libros:

Crear un libro: Ingresar título, autor(es), ISBN, género, año de publicación, descripción.
Leer libros: Listar todos los libros, buscar por título, autor o ISBN.
Actualizar un libro: Modificar cualquier campo.
Eliminar un libro: Solo si no está prestado actualmente.

Gestión de Autores:

Crear un autor: Ingresar nombre, apellido, fecha de nacimiento, biografía.
Leer autores: Listar todos, buscar por nombre.
Actualizar un autor: Modificar campos.
Eliminar un autor: Solo si no tiene libros asociados.

Gestión de Préstamos:

Registrar un préstamo: Seleccionar usuario, libro y fecha de préstamo (fecha de devolución estimada).
Visualizar préstamos: Lista de préstamos activos, históricos, por usuario o libro.
Gestionar devoluciones: Marcar un préstamo como devuelto con fecha real.
Validaciones: Un libro no puede prestarse si ya está prestado; usuarios no pueden tener más de X libros prestados simultáneamente.

Lógica de Negocio General:

Relaciones: Un libro puede tener múltiples autores; un autor puede tener múltiples libros; un usuario puede tener múltiples préstamos.
Restricciones: No permitir eliminación de entidades con dependencias activas.
Reportes básicos: Número de libros disponibles, préstamos pendientes, etc.
Requerimientos No Funcionales
Escalabilidad: Base de datos normalizada para manejar crecimiento.
Mantenibilidad: Código bien documentado, modular.
Seguridad: Hashing de contraseñas, protección contra inyección SQL.
Usabilidad: Interfaz responsive y fácil de usar.
Normalización de la Base de Datos
La base de datos utiliza un esquema relacional normalizado hasta la Tercera Forma Normal (3NF) para evitar redundancias, asegurar integridad y facilitar el mantenimiento.

Arquitectura del Sistema
Modelo-Vista-Controlador (MVC):

Modelo: Maneja la lógica de datos y consultas a la BD (e.g., clases UserModel, BookModel).

Vista: Plantillas HTML/PHP para la interfaz (e.g., login.php, books_list.php).

Controlador: Gestiona la lógica de negocio y enruta solicitudes (e.g., UserController, BookController).

Tecnologías: PHP (backend), MySQL (BD), HTML/CSS/JavaScript (frontend), Bootstrap (UI).