# Acreditación de Medios – Ordenación Episcopal

Sistema independiente de la Pastoral de Comunicaciones de la Diócesis de
Zacatecoluca Nuestra Señora de los Pobres, para registrar a los medios que
cubrirán la Ordenación Episcopal de Monseñor Ramiro Landaverde el 15 de agosto
de 2026.

## Funciones

- Formulario público con fotografía y validación de DUI.
- Portada institucional y formulario en páginas separadas.
- Código único de registro.
- Credencial individual con fotografía.
- Botones separados para imprimir y descargar PDF.
- Panel administrativo protegido con contraseña.
- Enlace público del formulario listo para copiar desde el panel.
- Eliminación segura de registros de prueba desde administración.
- Impresión y descarga PDF de credenciales desde cada registro.
- Búsqueda de comunicadores y reporte imprimible/PDF.
- Fotografías almacenadas dentro de MySQL para conservarlas en Railway.
- Diseño adaptable a computadora y celular.

## Ejecutar localmente

1. Crear una base MySQL llamada `acreditacion_medios`.
2. Copiar `.env.example` como `.env` y completar los datos.
3. Configurar esas variables en Apache/PHP o usar Docker.
4. Ejecutar con Docker:

```bash
docker build -t acreditacion-medios .
docker run --env-file .env -p 8080:80 acreditacion-medios
```

Abrir `http://localhost:8080`.

## Publicar en Railway

1. Subir este proyecto a GitHub.
2. Crear un proyecto en Railway desde el repositorio.
3. Agregar un servicio MySQL al mismo proyecto.
4. Railway proporcionará automáticamente `MYSQLHOST`, `MYSQLPORT`,
   `MYSQLDATABASE`, `MYSQLUSER` y `MYSQLPASSWORD`.
5. Agregar una variable `ADMIN_PASSWORD` con una contraseña segura.
6. Generar el dominio público del servicio web.

El sistema crea la tabla automáticamente en el primer acceso.
