# API Sanitaria

API en Laravel que simula la gestión de datos médicos de un servicio público de salud. Sin sesiones: el acceso se controla con un token propio que se genera al hacer login.

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve        # o con Herd: http://api-sanitaria.test
```

## Base de datos

| Tabla | Campos principales |
|---|---|
| `users` | name, email, password, **rol** (`paciente` / `medico`), fecha_nacimiento, **peso** (kg), **altura** (cm), **alergias** |
| `tokens` | user_id, token (único, 60 caracteres) |
| `historiales` | paciente_id, medico_id, fecha, motivo, diagnostico, tratamiento, observaciones |

### Usuarios del seeder (contraseña `password`)

| Email | Rol |
|---|---|
| medico@salud.test | médico |
| ana@salud.test | paciente |
| javier@salud.test | paciente |

## Autenticación

- `POST /api/login` devuelve un token nuevo.
- El token se envía en la cabecera `Authorization: Bearer <token>` o como parámetro `?token=<token>`.
- **CheckToken** (`token`): si falta el token o no es válido devuelve `401`.
- **CheckMedico** (`medico`): si el usuario no es médico devuelve `403`.
- El registro siempre crea pacientes; los médicos solo se crean desde el seeder.

## Endpoints

| Acceso | Método | Ruta | Descripción |
|---|---|---|---|
| Público | POST | `/api/registro` | Registrar paciente |
| Público | POST | `/api/login` | Login, devuelve el token |
| Token | GET | `/api/mi-ficha` | Mis datos médicos e historial |
| Token | PATCH | `/api/mi-ficha` | Actualizar mi peso y/o altura |
| Token | POST | `/api/logout` | Invalidar el token |
| Token + médico | GET | `/api/pacientes` | Lista de pacientes |
| Token + médico | GET | `/api/pacientes/{id}` | Ficha e historial completo de un paciente |
| Token + médico | POST | `/api/pacientes/{id}/historial` | Añadir una entrada al historial |
| Token + médico | PATCH | `/api/pacientes/{id}/alergias` | Modificar las alergias |

## Formato de respuesta

Todas las respuestas usan la misma estructura y todos los métodos tienen try/catch:

```json
{
    "success": true,
    "message": "Ficha médica de Ana García López.",
    "data": { }
}
```

Códigos: `200`/`201` correcto, `401` sin token o token no válido, `403` sin rol de médico, `404` paciente no encontrado, `422` datos no válidos, `500` error interno.

## Postman

**Workspace público:** https://www.postman.com/carlossaucedoafonso79-5111385/api-sanitaria

El workspace (y la carpeta `postman/` del repo) tiene la colección **API Sanitaria** con 23 peticiones en 4 carpetas: públicas, paciente, médico y logout. Incluye también los casos de error (401, 403, 404 y 422).

- Cada petición tiene guardado un **ejemplo con la respuesta real** de la API (pestaña *Examples*), así que se puede ver el resultado sin ejecutar nada.
- Cada petición tiene **tests** que comprueban el código HTTP y la estructura `success/message/data`.
- Los logins guardan los tokens en las variables `{{token_paciente}}` y `{{token_medico}}`, así que las peticiones se ejecutan en orden sin copiar tokens a mano.

### Cómo ejecutarla

1. Arrancar la API (ver *Instalación*).
2. En Postman, hacer *Fork* de la colección y de los entornos, o importar los JSON de `postman/`.
3. Elegir el entorno **API Sanitaria - artisan serve** (`http://127.0.0.1:8000/api`) o **API Sanitaria - Local** (Herd, `http://api-sanitaria.test/api`).
4. *Run collection*: pasan las 23 peticiones y los 57 tests.

> Si se usa Postman en el navegador, hay que elegir **Desktop Agent** (abajo a la derecha). El *Cloud Agent* manda las peticiones desde los servidores de Postman y no puede llegar a una API que está en local (`Couldn't resolve host`). Con la app de escritorio funciona directamente.
