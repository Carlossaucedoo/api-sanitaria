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

En la carpeta `postman/` están la colección **API Sanitaria** y el entorno **API Sanitaria - Local**. Las peticiones están ordenadas: los logins guardan los tokens en las variables `{{token_paciente}}` y `{{token_medico}}`, y cada petición tiene tests que comprueban el código y la estructura de la respuesta. Se puede ejecutar entera con el *Collection Runner*.
