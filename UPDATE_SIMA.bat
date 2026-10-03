@echo off
setlocal
cd /d "%~dp0"
set "SIMA_PHP=php"
where php >nul 2>nul
if errorlevel 1 (
    if exist "C:\xampp\php\php.exe" (
        set "SIMA_PHP=C:\xampp\php\php.exe"
    ) else (
        echo PHP tidak ditemukan. Jalankan dari terminal PHP atau tambahkan PHP ke PATH.
        pause
        exit /b 1
    )
)
if not exist ".env" (
    echo Salin .env dari proyek SIMA lama terlebih dahulu.
    pause
    exit /b 1
)
"%SIMA_PHP%" artisan config:clear
if errorlevel 1 goto failed
"%SIMA_PHP%" artisan route:clear
if errorlevel 1 goto failed
"%SIMA_PHP%" artisan view:clear
if errorlevel 1 goto failed
echo.
echo Update SIMA siap. Jalankan server seperti biasa.
echo Database dan dokumen lama tidak dihapus.
pause
exit /b 0
:failed
echo Proses berhenti. Periksa pesan error di atas.
pause
exit /b 1
