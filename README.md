# Tienda La Firma (PHP + MySQL para XAMPP)

## Instalación
1. Copia la carpeta `tienda_la_firma` dentro de `C:\xampp\htdocs\`.
2. En XAMPP enciende **Apache** y **MySQL**.
3. phpMyAdmin (`http://localhost/phpmyadmin`) > **Importar** > `database/tienda_la_firma.sql` > Continuar.
   (Ojo: el script recrea las tablas; si ya tenías usuarios, se borran.)
4. Abre **`http://localhost/tienda_la_firma/`** (NO uses Live Server ni abras los archivos con doble clic: PHP solo funciona a través de Apache).
5. Si algo falla, abre `http://localhost/tienda_la_firma/diagnostico.php` y bórralo al terminar.

Si tu MySQL tiene contraseña o usa otro puerto, cámbialo en `config/db.php`.
