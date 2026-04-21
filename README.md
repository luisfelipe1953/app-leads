# OMC Leads API

API REST para gestión de leads de **One Million Copy SAS**. Permite registrar, consultar, actualizar y eliminar leads provenientes de distintos canales de marketing, con integración de IA para generar resúmenes ejecutivos.

## Tecnologías

| Capa | Tecnología | Por qué |
|------|-----------|---------|
| Framework | Laravel 11 | Ecosistema maduro, validaciones y ORM integrados |
| Lenguaje | PHP 8.3 | Tipado estricto, rendimiento mejorado |
| Base de datos | MySQL | Relacional, fácil de migrar y auditar |
| ORM | Eloquent | Soft deletes, factories y seeders nativos |
| Arquitectura | Repository Pattern | Desacopla acceso a datos de lógica de negocio |
| IA | OpenAI GPT-3.5 | API confiable con fallback mock documentado |
| Docs | L5-Swagger | Documentación automática OpenAPI 3.0 |
| Auth | API Key (header) | Simple y efectivo para APIs B2B internas |
| Rate limiting | Laravel throttle | 60 req/min por IP, nativo del framework |

## Instalación

### Opción 1 — Local (Laragon / XAMPP)

**Requisitos:** PHP 8.2+, Composer, MySQL

```bash
git clone <repo-url> omc-leads
cd omc-leads

composer install

cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales:

```env
DB_DATABASE=omc_leads
DB_USERNAME=root
DB_PASSWORD=

APP_API_KEY=mi-clave-secreta
OPENAI_API_KEY=sk-...  # opcional, usa mock si está vacío
```

Crear la base de datos y ejecutar migraciones:

```bash
# Crear DB en MySQL
mysql -u root -e "CREATE DATABASE omc_leads;"

php artisan migrate
```

Ejecutar el seed:

```bash
php artisan db:seed
```

Iniciar el servidor:

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

### Opción 2 — Docker

**Requisitos:** Docker y Docker Compose

```bash
git clone <repo-url> omc-leads
cd omc-leads

cp .env.example .env
php artisan key:generate   # o genera la key manualmente

docker-compose up -d
```

La API estará en `http://localhost:8000` (MySQL en puerto 3307).

## Autenticación

Todos los endpoints (excepto `/webhook`) requieren el header:

```
X-API-KEY: <tu-api-key>
```

El valor debe coincidir con `APP_API_KEY` en `.env`.

## Endpoints

### Leads

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/leads` | Listar leads (paginado + filtros) |
| `POST` | `/api/leads` | Crear lead |
| `GET` | `/api/leads/stats` | Estadísticas generales |
| `GET` | `/api/leads/{id}` | Obtener lead por ID |
| `PATCH` | `/api/leads/{id}` | Actualizar lead |
| `DELETE` | `/api/leads/{id}` | Eliminar lead (soft delete) |
| `POST` | `/api/leads/ai/summary` | Resumen IA de leads |
| `POST` | `/api/leads/webhook` | Webhook estilo Typeform |

### Parámetros de `GET /api/leads`

| Param | Tipo | Descripción |
|-------|------|-------------|
| `page` | int | Página (default: 1) |
| `limit` | int | Resultados por página (default: 15) |
| `fuente` | string | Filtrar por fuente |
| `fecha_inicio` | date | Filtrar desde fecha (YYYY-MM-DD) |
| `fecha_fin` | date | Filtrar hasta fecha (YYYY-MM-DD) |

## Ejemplos de uso

### Crear un lead

```bash
curl -X POST http://localhost:8000/api/leads \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: mi-clave-secreta" \
  -d '{
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "telefono": "+573001234567",
    "fuente": "instagram",
    "producto_interes": "Curso de Marketing Digital",
    "presupuesto": 500
  }'
```

### Listar leads con filtros

```bash
curl "http://localhost:8000/api/leads?fuente=instagram&page=1&limit=10" \
  -H "X-API-KEY: mi-clave-secreta"
```

### Estadísticas

```bash
curl http://localhost:8000/api/leads/stats \
  -H "X-API-KEY: mi-clave-secreta"
```

### Resumen con IA

```bash
curl -X POST http://localhost:8000/api/leads/ai/summary \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: mi-clave-secreta" \
  -d '{
    "fuente": "instagram",
    "fecha_inicio": "2024-01-01",
    "fecha_fin": "2024-12-31"
  }'
```

### Webhook (simulando Typeform)

```bash
curl -X POST http://localhost:8000/api/leads/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "form_response": {
      "answers": [
        {"field": {"ref": "nombre"}, "text": "María López"},
        {"field": {"ref": "email"}, "email": "maria@example.com"},
        {"field": {"ref": "fuente"}, "text": "landing_page"},
        {"field": {"ref": "presupuesto"}, "number": 800}
      ]
    }
  }'
```

## Documentación Swagger

Accede a la documentación interactiva en:

```
http://localhost:8000/api/documentation
```

Para regenerar el spec:

```bash
php artisan l5-swagger:generate
```

## Ejecutar tests

```bash
php artisan test
# o
./vendor/bin/phpunit
```

## Notas sobre la integración IA

- Si `OPENAI_API_KEY` no está configurada, el endpoint `/api/leads/ai/summary` retorna un **mock documentado** con el mismo formato que la respuesta real.
- La arquitectura está preparada para cambiar el LLM: solo se modifica `AiSummaryService.php`.
- Se usa `gpt-3.5-turbo` por costo/velocidad. Para mayor calidad usar `gpt-4o`.
