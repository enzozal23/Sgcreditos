/**
 * AJAX Setup para Laravel
 * Configuración global de AJAX para el manejo de tokens CSRF y configuraciones básicas
 */

// Configuración global de AJAX
$(document).ready(function() {
    // Configurar el token CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Configuración adicional para peticiones AJAX
    $.ajaxSetup({
        beforeSend: function(xhr) {
            // Mostrar indicador de carga si existe
            if (typeof showLoading === 'function') {
                showLoading();
            }
        },
        complete: function() {
            // Ocultar indicador de carga si existe
            if (typeof hideLoading === 'function') {
                hideLoading();
            }
        },
        error: function(xhr, status, error) {
            // Manejo global de errores AJAX
            console.error('Error AJAX:', status, error);
            
            // Si es un error 401 (no autorizado), redirigir al login
            if (xhr.status === 401) {
                window.location.href = '/login';
                return;
            }
            
            // Si es un error 403 (prohibido), mostrar mensaje
            if (xhr.status === 403) {
                if (typeof showError === 'function') {
                    showError('Acceso Denegado', 'No tiene permisos para realizar esta acción.');
                }
                return;
            }
            
            // Si es un error 422 (validación), manejar en el callback específico
            if (xhr.status === 422) {
                return;
            }
            
            // Para otros errores, mostrar mensaje genérico
            if (typeof showError === 'function') {
                showError('Error', 'Ha ocurrido un error inesperado. Por favor, intente nuevamente.');
            }
        }
    });
});

// Función para mostrar indicador de carga
function showLoading() {
    // Crear overlay de carga si no existe
    if ($('#loading-overlay').length === 0) {
        $('body').append(`
            <div id="loading-overlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
            ">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `);
    }
    $('#loading-overlay').show();
}

// Función para ocultar indicador de carga
function hideLoading() {
    $('#loading-overlay').hide();
}

// Función para hacer peticiones AJAX con manejo de errores mejorado
function ajaxRequest(url, method, data, successCallback, errorCallback) {
    $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: 'json',
        success: function(response) {
            if (successCallback) {
                successCallback(response);
            }
        },
        error: function(xhr, status, error) {
            if (errorCallback) {
                errorCallback(xhr, status, error);
            }
        }
    });
}

// Función para validar formularios antes de enviar
function validateForm(formSelector) {
    const form = $(formSelector);
    const requiredFields = form.find('[required]');
    let isValid = true;
    
    requiredFields.each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            isValid = false;
        } else {
            field.removeClass('is-invalid');
        }
    });
    
    return isValid;
}

// Función para limpiar errores de validación
function clearValidationErrors(formSelector) {
    $(formSelector).find('.is-invalid').removeClass('is-invalid');
    $(formSelector).find('.invalid-feedback').text('');
}
