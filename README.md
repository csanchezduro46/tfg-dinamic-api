# TFG – Dinamic API

Plataforma desarrollada como Trabajo Final de Grado por Cristina Sánchez Duro, que permite integrar plataformas externas (como Shopify o PrestaShop) con bases de datos internas a través de un sistema de conexiones, mapeo de campos y sincronizaciones manuales o programadas.

---

## 🚀 Tecnologías utilizadas

- **Frontend:** Angular 19 (TypeScript) + Tailwind CSS  
- **Backend:** Laravel 12 (PHP 8.2)  
- **Base de datos:** PostgreSQL  
- **Documentación API:** Scramble (OpenAPI)  
- **Pruebas API:** Postman

---

## 📁 Estructura del proyecto

```
TFG-DINAMIC-API/
├── dinamic-api-back/       # Backend Laravel
├── dinamic-api-ui/         # Frontend Angular
├── config/                 # Configuración Docker y .env
├── docker-compose.yaml     # Contenedor para despliegue completo
```

---

## ⚙️ Instalación rápida

### 🔧 Backend (Laravel)

```bash
Dentro del contenedor docker:
cd dinamic-api-back
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
```

> Configura `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=db_dinamicapi
DB_USERNAME=dinamicapi
DB_PASSWORD=Lk93ajsd8Mno1ZplqB 
```

> Configura correo electrónico en `.env`:
Los correos llegarán al buzón de pruebas al trabajar en el entorno develop
En producción se cambiarán por los datos del servidor de correo elegido
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=4439aef9d0eee4
MAIL_PASSWORD=5c42c8a45bb599
#MAIL_ENCRYPTION=tls
#MAIL_FROM_ADDRESS=noreply@dinamicapi.com
MAIL_FROM_NAME="${APP_NAME}"
```


Lanzar servidor local:
```bash
php artisan serve
```

---

### 💻 Frontend (Angular)

```bash
cd dinamic-api-ui
npm install

ng serve
```

Archivo `src/environments/environment.ts`:

```ts
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8085'
};
```

---

## 🐳 Despliegue con Docker

1. Revisar archivos `.env` en `/config/`
2. Lanzar contenedores:

```bash
docker-compose up -d --build
```

```bash
Dentro del contenedor docker:
cd dinamic-api-back
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
```

3. Accede a:
- **API Laravel** → `http://localhost:8085`
- **Angular UI** → `http://localhost:4200`

---

## 🧪 Pruebas con Postman

1. Abre Postman
2. Importa la colección:
   `/postman/Dinamic API.postman_collection.json`
3. Selecciona el entorno `local_environment`
4. Prueba login, conexiones, mapeos, ejecuciones

---

## 🧼 Datos precargados

Los seeders incluyen:

- Usuario administrador
- Plataformas de prueba (Shopify, PrestaShop)
- Versiones de plataforma
- APIs simuladas con payloads

---

## 📋 Documentación de la API

La plataforma utiliza **Scramble** para generar documentación OpenAPI:

- Acceso en: `http://localhost:8085/docs`

Generar archivo exportable:

```bash
php artisan scramble:export
```

Archivo `openapi.yaml` generado en `storage/app/`.

---

## ⏱ Sincronizaciones programadas

Para ejecutar las tareas programadas, lanzar manualmente:

```bash
php artisan app:scheduled-executions
```

---

## 🧪 Testing E2E con Cypress

1. Instalar Cypress:

```bash
cd dinamic-api-ui
npm install cypress --save-dev
```

2. Ejecutar interfaz gráfica:

```bash
npx cypress open
```

3. Test completo incluido:
`cypress/e2e/flujo-mapeo-sync.cy.ts`

---

## 👤 Autora

**Cristina Sánchez Duro**  
Grado en Ingeniería de Telecomunicaciones – UOC  
Trabajo Final de Grado – Mayo 2025

---