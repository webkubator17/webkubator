# Webkubator

Website utama Webkubator berbasis PHP, HTML, CSS, dan JavaScript vanilla.

## Deployment cPanel

Repository ini menggunakan `.cpanel.yml` untuk menyalin file website ke `/home/webk6347/public_html/`.

File WordPress lama, database SQL, `wp-config.php`, cache, dan kredensial tidak disimpan di repository ini.

## Automatic deployment

Setiap push ke branch `main` menjalankan GitHub Actions dan mengunggah website ke hosting melalui explicit FTPS. FTP credentials disimpan sebagai GitHub Actions Secrets, bukan di dalam source code.

