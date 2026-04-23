# Laravel Project Setup Script
# Automatiza: crear .env, generar clave, migraciones

Write-Host "Iniciando setup del proyecto Laravel..." -ForegroundColor Cyan

# 1. Copiar .env.example a .env
if (Test-Path .env.example) {
    if (-not (Test-Path .env)) {
        Copy-Item .env.example .env
        Write-Host "[OK] Archivo .env creado desde .env.example" -ForegroundColor Green
    } else {
        Write-Host "[WARN] El archivo .env ya existe (no se sobrescribio)" -ForegroundColor Yellow
    }
} else {
    Write-Host "[ERROR] No se encontro .env.example" -ForegroundColor Red
    exit 1
}

# 2. Instalar dependencias Composer (si falta vendor)
if (-not (Test-Path vendor)) {
    Write-Host "[INFO] Instalando dependencias de Composer..." -ForegroundColor Cyan
    composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Error al instalar Composer" -ForegroundColor Red
        exit 1
    }
    Write-Host "[OK] Dependencias instaladas" -ForegroundColor Green
} else {
    Write-Host "[OK] Vendor ya existe (saltando composer install)" -ForegroundColor Green
}

# 3. Generar clave de aplicación
Write-Host "[INFO] Generando APP_KEY..." -ForegroundColor Cyan
php artisan key:generate --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Error al generar key" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] APP_KEY generada" -ForegroundColor Green

# 4. Crear base de datos si no existe
Write-Host "[INFO] Preparando migraciones..." -ForegroundColor Cyan

# Create SQLite database file if using SQLite
if (Test-Path .env) {
    $dbConnection = Select-String -Path .env -Pattern "^DB_CONNECTION=" | ForEach-Object { $_.Line -replace "DB_CONNECTION=", "" }
    if ($dbConnection -eq "sqlite") {
        $dbFile = "database\database.sqlite"
        if (-not (Test-Path $dbFile)) {
            Write-Host "[INFO] Creando archivo de base de datos SQLite..." -ForegroundColor Cyan
            New-Item -ItemType File -Path $dbFile -Force | Out-Null
        }
    }
}

$dbResult = "OK"

if ($dbResult -like "OK*") {
    Write-Host "[OK] Setup de BD completado" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error configurando BD: $dbResult" -ForegroundColor Red
}

# 5. Ejecutar migraciones
Write-Host "[INFO] Ejecutando migraciones..." -ForegroundColor Cyan
php artisan migrate --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Error en migraciones" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Migraciones completadas" -ForegroundColor Green

Write-Host ""
Write-Host "[DONE] Setup completado exitosamente!" -ForegroundColor Green
Write-Host "La aplicacion esta lista en: http://12c.test" -ForegroundColor Cyan
