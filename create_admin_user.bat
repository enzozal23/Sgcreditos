@echo off
echo ========================================
echo    CREADOR DE USUARIO ADMINISTRADOR
echo ========================================
echo.

REM Verificar si PHP está instalado
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP no está instalado o no está en el PATH
    echo Por favor, instala PHP y asegúrate de que esté en el PATH del sistema
    pause
    exit /b 1
)

REM Verificar si el archivo PHP existe
if not exist "create_admin_user.php" (
    echo ERROR: No se encuentra el archivo create_admin_user.php
    pause
    exit /b 1
)

echo Ejecutando script de creación de usuario...
echo.

REM Ejecutar el script PHP
php create_admin_user.php

echo.
echo Presiona cualquier tecla para continuar...
pause >nul
