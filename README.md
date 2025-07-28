# Prueba-Tecnica-Grupo-Soluciones-Informaticas
## Requisitos
- PHP 8.2+
- Composer
- MySQL 8.0 / MariaDB 10.6
- Node.js 16+

## Instalación
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan restart:project
npm install
npm run dev
php artisan serve
