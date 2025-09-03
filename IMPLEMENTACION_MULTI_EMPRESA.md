# Implementación del Sistema Multi-Empresa

Este documento describe la implementación del sistema multi-empresa en la aplicación Laravel.

## Cambios Implementados

### 1. Base de Datos

#### Nuevas Migraciones Creadas:
- `2025_08_22_000001_create_empresas_table.php` - Tabla principal de empresas
- `2025_08_22_000002_add_empresa_id_to_users_table.php` - Agregar empresa_id a usuarios
- `2025_08_22_000003_add_empresa_id_to_clientes_table.php` - Agregar empresa_id a clientes
- `2025_08_22_000004_add_empresa_id_to_creditos_table.php` - Agregar empresa_id a créditos
- `2025_08_22_000005_add_empresa_id_to_tipo_clientes_table.php` - Agregar empresa_id a tipos de cliente
- `2025_08_22_000006_add_empresa_id_to_tipo_creditos_table.php` - Agregar empresa_id a tipos de crédito

#### Estructura de la Tabla Empresas:
```sql
- id (primary key)
- nombre
- razon_social
- cuit (unique)
- email
- telefono
- direccion
- ciudad
- provincia
- codigo_postal
- logo
- activo (boolean)
- timestamps
```

### 2. Modelos

#### Nuevo Modelo:
- `Empresa.php` - Modelo principal con relaciones a todas las entidades

#### Modelos Actualizados:
- `User.php` - Agregada relación con Empresa
- `Cliente.php` - Agregada relación con Empresa y trait FiltroEmpresa

### 3. Middleware y Seguridad

#### Nuevo Middleware:
- `VerificarEmpresa.php` - Verifica que el usuario tenga empresa asignada

#### Filtrado por Empresa:
- Scope `deMiEmpresa()` en modelos - Filtra automáticamente por empresa del usuario autenticado

### 4. Controladores

#### Nuevo Controlador:
- `EmpresaController.php` - CRUD completo para gestión de empresas

### 5. Comandos Artisan

#### Nuevo Comando:
- `AsignarEmpresaUsuarios.php` - Asigna empresa a usuarios existentes

### 6. Seeders

#### Nuevo Seeder:
- `EmpresaSeeder.php` - Crea empresas de ejemplo

## Instrucciones de Implementación

### Paso 1: Ejecutar las Migraciones
```bash
php artisan migrate
```

### Paso 2: Ejecutar los Seeders
```bash
php artisan db:seed --class=EmpresaSeeder
```

### Paso 3: Asignar Empresa a Usuarios Existentes
```bash
php artisan usuarios:asignar-empresa
```

### Paso 4: Aplicar el Middleware (Opcional)
Si deseas que todas las rutas verifiquen la empresa del usuario, agrega el middleware a las rutas:

```php
Route::middleware(['auth', 'verificar.empresa'])->group(function () {
    // Rutas protegidas
});
```

## Funcionalidades del Sistema

### 1. Aislamiento de Datos
- Cada empresa ve solo sus propios datos
- Los usuarios solo pueden acceder a información de su empresa
- Filtrado automático en todos los modelos que usen el trait FiltroEmpresa

### 2. Gestión de Empresas
- CRUD completo para empresas
- Activación/desactivación de empresas
- Validación de datos únicos (CUIT)
- Prevención de eliminación si hay datos asociados

### 3. Seguridad
- Verificación automática de empresa en cada request
- Middleware que valida la empresa del usuario
- Filtrado automático en consultas de base de datos

## Uso del Scope deMiEmpresa

Para aplicar el filtrado automático por empresa a cualquier modelo, simplemente agrega el scope `deMiEmpresa()`:

```php
// En el modelo
public function scopeDeMiEmpresa($query)
{
    if (auth()->check() && auth()->user()->empresa_id) {
        return $query->where('empresa_id', auth()->user()->empresa_id);
    }
    return $query;
}

// En el controlador
$clientes = Cliente::deMiEmpresa()->get();
```

### Funcionalidades del Scope:
- **Filtrado Automático**: Filtra por empresa_id del usuario autenticado
- **Seguridad**: Previene acceso a datos de otras empresas
- **Flexibilidad**: Se puede combinar con otros scopes y métodos
- **Simplicidad**: No requiere traits adicionales

## Consideraciones Importantes

### 1. Datos Existentes
- Los usuarios existentes deben tener empresa_id asignado
- Usar el comando `usuarios:asignar-empresa` para asignar empresa por defecto

### 2. Nuevos Usuarios
- Al crear usuarios, asegurarse de asignar empresa_id
- El sistema no permitirá usuarios sin empresa asignada

### 3. Migración de Datos
- Si hay datos existentes, considerar crear una empresa por defecto
- Asignar empresa_id a todos los registros existentes

### 4. Rendimiento
- Los índices en empresa_id mejoran el rendimiento de las consultas
- El filtrado automático puede afectar consultas complejas

## Próximos Pasos Recomendados

1. **Crear Vistas**: Implementar las vistas para gestión de empresas
2. **Permisos**: Agregar sistema de permisos por empresa
3. **Configuración**: Permitir configuración específica por empresa
4. **Auditoría**: Implementar logs de auditoría por empresa
5. **Backup**: Configurar backups separados por empresa

## Solución de Problemas

### Error: "Column 'empresa_id' not found"
- Verificar que las migraciones se ejecutaron correctamente
- Ejecutar `php artisan migrate:status` para ver el estado

### Error: "User doesn't have empresa_id"
- Ejecutar `php artisan usuarios:asignar-empresa`
- Verificar que existe al menos una empresa en la base de datos

### Datos no se filtran por empresa
- Verificar que el modelo use el scope `deMiEmpresa()`
- Verificar que el usuario autenticado tenga empresa_id
- Verificar que la columna empresa_id existe en la tabla
