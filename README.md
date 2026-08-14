# Acreditación de Medios – Ordenación Episcopal

Sistema independiente de la Pastoral de Comunicaciones de la Diócesis de
Zacatecoluca Nuestra Señora de los Pobres, para registrar a los medios que
cubrirán la Ordenación Episcopal de Monseñor Ramiro Landaverde el 15 de agosto
de 2026.

## Funciones

- Formulario público con fotografía y validación de DUI.
- Portada institucional y formulario en páginas separadas.
- Código único de registro.
- Solicitudes guardadas como `Pendiente` hasta que un administrador las revise.
- Aprobación y rechazo manual desde el panel administrativo.
- Edición administrativa de todos los datos y de la fotografía sin alterar el código ni el estado.
- Credencial individual con fotografía, disponible únicamente al aprobar.
- Correo de aprobación o rechazo mediante Resend.
- Enlace privado para descargar la credencial aprobada.
- Botones separados para imprimir y descargar PDF.
- Panel administrativo protegido con contraseña.
- Enlace público del formulario listo para copiar desde el panel.
- Eliminación segura de registros de prueba desde administración.
- Impresión y descarga PDF de credenciales desde cada registro.
- Acceso directo `Imprimir / PDF` para cada solicitud aprobada.
- Búsqueda de comunicadores y reporte imprimible/PDF.
- Fotografías almacenadas dentro de MySQL para conservarlas en Railway.
- Diseño adaptable a computadora y celular.

## Ejecutar localmente

1. Crear una base MySQL llamada `acreditacion_medios`.
2. Copiar `.env.example` como `.env` y completar los datos.
3. Configurar esas variables en Apache/PHP o usar Docker.
4. Para iniciar la aplicación y MySQL juntos, ejecutar:

```bash
docker compose up --build
```

Abrir `http://localhost:8080`. El panel administrativo está en
`http://localhost:8080/admin/`; si no se define otra contraseña en `.env`, la
contraseña local de prueba es `admin123`.

Para detener los contenedores, usar `docker compose down`. Los registros se
conservan en un volumen local de Docker. Para probar los correos, completar
`RESEND_API_KEY`, `MAIL_FROM`, `APP_URL=http://localhost:8080` y
`CREDENTIAL_SECRET` en `.env` antes de iniciar.

## Publicar en Railway

1. Subir este proyecto a GitHub.
2. Crear un proyecto en Railway desde el repositorio.
3. Agregar un servicio MySQL al mismo proyecto.
4. Railway proporcionará automáticamente `MYSQLHOST`, `MYSQLPORT`,
   `MYSQLDATABASE`, `MYSQLUSER` y `MYSQLPASSWORD`.
5. Agregar las variables de la aplicación:

   - `ADMIN_PASSWORD`: contraseña segura del panel.
   - `RESEND_API_KEY`: llave API del proveedor Resend.
   - `MAIL_FROM`: remitente autorizado, por ejemplo
     `Logística de Medios <acreditaciones@tu-dominio.com>`.
   - `APP_URL`: dominio público completo, sin diagonal final.
   - `CREDENTIAL_SECRET`: cadena larga y aleatoria para proteger los enlaces.

6. Generar el dominio público del servicio web y usarlo como `APP_URL`.

El dominio de `MAIL_FROM` debe estar verificado en Resend. Si el correo no se
puede enviar, la solicitud permanece como `Pendiente` para que el administrador
pueda intentarlo nuevamente.

El sistema crea la tabla automáticamente en el primer acceso. En instalaciones
existentes agrega únicamente la columna `estado`, sin eliminar registros ni
modificar las demás columnas.
