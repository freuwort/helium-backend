# Helium Backend

**Helium Backend is the backend API behind the Helium CMS.**  
[Helium Compose](https://github.com/freuwort/helium-compose) | Helium Backend | [Helium Frontend](https://github.com/freuwort/helium-frontend) | [Helium Screens](https://github.com/freuwort/helium-screens)

## Setup

```bash
composer install

php artisan key:generate

php artisan storage:link

php artisan migrate
```

## Development

Start the development server on `http://localhost:8000`
```bash
php artisan serve

php artisan queue:work
```

Make sure to have the following services and packages installed, configured and available:
```
- MySQL (MariaDB)
- Redis (Valkey)
- FFMPEG
- ImageMagick
- PhpImagick
- xPdf (pdftoppm)
```

## Build

To build the Helium Backend image
```bash
docker build -t helium-backend .
```

## Security Vulnerabilities

If you discover a security vulnerability within the Helium backend, please send an email to Alyx Freuwört via [contact@freuwort.com](mailto:contact@freuwort.com).  
We will address all security vulnerabilities promptly.

## License

The Helium backend is an open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
