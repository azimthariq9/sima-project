#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")"
[ -f .env ] || { echo 'Salin .env dari proyek SIMA lama terlebih dahulu.'; exit 1; }
php artisan config:clear
php artisan route:clear
php artisan view:clear
printf '%s\n' 'Update SIMA siap. Jalankan server seperti biasa. Database dan dokumen lama tidak dihapus.'
