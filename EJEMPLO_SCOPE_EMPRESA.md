# Ejemplo de Implementación del Scope deMiEmpresa

Este archivo muestra cómo implementar el filtrado por empresa en diferentes modelos de la aplicación.

## 1. Modelo TipoCliente

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipoCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'identificador',
        'descripcion',
        'activo',
        'empresa_id' // Agregar este campo
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }

    /**
     * Scope para filtrar por empresa específica
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
```

## 2. Modelo TipoCredito

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipoCredito extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'empresa_id' // Agregar este campo
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }
}
```

## 3. Modelo Credito

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credito extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'empresa_id' // Agregar este campo
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }
}
```

## 4. Uso en Controladores

### TipoClienteController
```php
public function index()
{
    $tiposCliente = TipoCliente::deMiEmpresa()
        ->where('activo', true)
        ->orderBy('identificador')
        ->get();
    
    return view('tipos_clientes.index', compact('tiposCliente'));
}

public function getData()
{
    $tiposCliente = TipoCliente::deMiEmpresa()
        ->orderBy('identificador')
        ->get();
    
    // ... procesar datos para DataTable
}
```

### TipoCreditoController
```php
public function index()
{
    $tiposCredito = TipoCredito::deMiEmpresa()
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();
    
    return view('tipos_creditos.index', compact('tiposCredito'));
}
```

## 5. Verificaciones de Seguridad en Controladores

### Al crear registros
```php
public function store(Request $request)
{
    $data = $request->all();
    
    // Asignar empresa_id del usuario autenticado
    if (auth()->check() && auth()->user()->empresa_id) {
        $data['empresa_id'] = auth()->user()->empresa_id;
    }
    
    $tipoCliente = TipoCliente::create($data);
    
    return redirect()->route('tipos-clientes.index')
        ->with('success', 'Tipo de cliente creado exitosamente.');
}
```

### Al actualizar registros
```php
public function update(Request $request, TipoCliente $tipoCliente)
{
    // Verificar que pertenece a la empresa del usuario
    if (auth()->check() && auth()->user()->empresa_id && 
        $tipoCliente->empresa_id !== auth()->user()->empresa_id) {
        return redirect()->back()
            ->with('error', 'No tienes permisos para editar este tipo de cliente.');
    }
    
    $tipoCliente->update($request->all());
    
    return redirect()->route('tipos-clientes.index')
        ->with('success', 'Tipo de cliente actualizado exitosamente.');
}
```

### Al eliminar registros
```php
public function destroy(TipoCliente $tipoCliente)
{
    // Verificar que pertenece a la empresa del usuario
    if (auth()->check() && auth()->user()->empresa_id && 
        $tipoCliente->empresa_id !== auth()->user()->empresa_id) {
        return redirect()->back()
            ->with('error', 'No tienes permisos para eliminar este tipo de cliente.');
    }
    
    $tipoCliente->delete();
    
    return redirect()->route('tipos-clientes.index')
        ->with('success', 'Tipo de cliente eliminado exitosamente.');
}
```

## 6. Ventajas de este Enfoque

1. **Simplicidad**: No requiere traits adicionales
2. **Flexibilidad**: Se puede usar solo cuando sea necesario
3. **Seguridad**: Previene acceso a datos de otras empresas
4. **Mantenibilidad**: Fácil de entender y modificar
5. **Performance**: Solo agrega una condición WHERE cuando es necesario

## 7. Consideraciones

- **Siempre usar** `deMiEmpresa()` en consultas que devuelven listas
- **Verificar empresa** en operaciones de CRUD individuales
- **Asignar empresa_id** automáticamente al crear registros
- **Mantener consistencia** en todos los modelos relacionados
