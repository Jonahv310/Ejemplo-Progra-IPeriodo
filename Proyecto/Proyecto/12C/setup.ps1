# Laravel Project Setup Script
# Automatiza: crear .env, generar clave, migraciones

Write-Host "🚀 Iniciando setup del proyecto Laravel..." -ForegroundColor Cyan

# 1. Copiar .env.example a .env
if (Test-Path .env.example) {
    if (-not (Test-Path .env)) {
        Copy-Item .env.example .env
        Write-Host "✓ Archivo .env creado desde .env.example" -ForegroundColor Green
    } else {
        Write-Host "⚠ El archivo .env ya existe (no se sobrescribió)" -ForegroundColor Yellow
    }
} else {
    Write-Host "✗ No se encontró .env.example" -ForegroundColor Red
    exit 1
}

# 2. Instalar dependencias Composer (si falta vendor)
if (-not (Test-Path vendor)) {
    Write-Host "📦 Instalando dependencias de Composer..." -ForegroundColor Cyan
    composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) {
        Write-Host "✗ Error al instalar Composer" -ForegroundColor Red
        exit 1
    }
    Write-Host "✓ Dependencias instaladas" -ForegroundColor Green
} else {
    Write-Host "✓ Vendor ya existe (saltando composer install)" -ForegroundColor Green
}

# 3. Generar clave de aplicación
Write-Host "🔑 Generando APP_KEY..." -ForegroundColor Cyan
php artisan key:generate --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Error al generar key" -ForegroundColor Red
    exit 1
}
Write-Host "✓ APP_KEY generada" -ForegroundColor Green

# 4. Crear base de datos si no existe
Write-Host "🗄️ Preparando base de datos..." -ForegroundColor Cyan
$script = @'
<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `12c` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "OK";
} catch (Throwable $e) {
    echo "FAIL:" . $e->getMessage();
}
'@

Set-Content -Path storage\app\create_db.php -Value $script
$dbResult = php storage\app\create_db.php
Remove-Item storage\app\create_db.php

if ($dbResult -like "OK*") {
    Write-Host "✓ Base de datos lista" -ForegroundColor Green
} else {
    Write-Host "✗ Error configurando BD: $dbResult" -ForegroundColor Red
    exit 1
}

# 5. Ejecutar migraciones
Write-Host "🔄 Ejecutando migraciones..." -ForegroundColor Cyan
php artisan migrate --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Error en migraciones" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Migraciones completadas" -ForegroundColor Green

Write-Host "`n✅ Setup completado exitosamente!" -ForegroundColor Green
Write-Host "La aplicación está lista en: http://12c.test" -ForegroundColor Cyan
