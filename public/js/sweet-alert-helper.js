/**
 * SweetAlert Helper Functions
 * Funciones auxiliares para el manejo de alertas con SweetAlert
 */

// Configuración global de SweetAlert
const SweetAlertConfig = {
    confirmButtonText: 'Confirmar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    reverseButtons: true
};

/**
 * Confirmación de eliminación
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 * @param {string} itemName - Nombre del elemento a eliminar
 * @returns {Promise} - Promise que se resuelve si se confirma
 */
function confirmDelete(title = '¿Está seguro?', text = 'Esta acción no se puede deshacer.', itemName = '') {
    const message = itemName ? `¿Está seguro de que desea eliminar "${itemName}"?` : text;
    
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: SweetAlertConfig.confirmButtonText,
        cancelButtonText: SweetAlertConfig.cancelButtonText,
        confirmButtonColor: SweetAlertConfig.confirmButtonColor,
        cancelButtonColor: SweetAlertConfig.cancelButtonColor,
        reverseButtons: SweetAlertConfig.reverseButtons
    });
}

/**
 * Confirmación general
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 * @param {string} icon - Icono (success, error, warning, info, question)
 * @returns {Promise} - Promise que se resuelve si se confirma
 */
function confirmAction(title = '¿Confirmar acción?', text = '¿Está seguro de que desea realizar esta acción?', icon = 'question') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: SweetAlertConfig.confirmButtonText,
        cancelButtonText: SweetAlertConfig.cancelButtonText,
        confirmButtonColor: SweetAlertConfig.confirmButtonColor,
        cancelButtonColor: SweetAlertConfig.cancelButtonColor,
        reverseButtons: SweetAlertConfig.reverseButtons
    });
}

/**
 * Mensaje de éxito
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 * @param {number} timer - Tiempo en ms para auto-cerrar (opcional)
 */
function showSuccess(title = '¡Éxito!', text = 'La operación se completó correctamente.', timer = 3000) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        timer: timer,
        timerProgressBar: true,
        showConfirmButton: timer === false
    });
}

/**
 * Mensaje de error
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 * @param {string} error - Detalles del error (opcional)
 */
function showError(title = 'Error', text = 'Ha ocurrido un error inesperado.', error = null) {
    let fullText = text;
    if (error) {
        fullText += `\n\nDetalles: ${error}`;
    }
    
    Swal.fire({
        title: title,
        text: fullText,
        icon: 'error',
        confirmButtonText: 'Entendido'
    });
}

/**
 * Mensaje de advertencia
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 */
function showWarning(title = 'Advertencia', text = 'Por favor, revise la información proporcionada.') {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}

/**
 * Mensaje informativo
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 */
function showInfo(title = 'Información', text = 'Información importante para el usuario.') {
    Swal.fire({
        title: title,
        text: text,
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

/**
 * Input prompt
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 * @param {string} inputType - Tipo de input (text, email, password, number, etc.)
 * @param {string} placeholder - Placeholder del input
 * @returns {Promise} - Promise que se resuelve con el valor ingresado
 */
function showInput(title = 'Ingrese información', text = 'Por favor, ingrese la información requerida.', inputType = 'text', placeholder = '') {
    return Swal.fire({
        title: title,
        text: text,
        input: inputType,
        inputPlaceholder: placeholder,
        showCancelButton: true,
        confirmButtonText: SweetAlertConfig.confirmButtonText,
        cancelButtonText: SweetAlertConfig.cancelButtonText,
        confirmButtonColor: SweetAlertConfig.confirmButtonColor,
        cancelButtonColor: SweetAlertConfig.cancelButtonColor,
        reverseButtons: SweetAlertConfig.reverseButtons,
        inputValidator: (value) => {
            if (!value) {
                return 'Debe ingresar un valor';
            }
        }
    });
}

/**
 * Loading spinner
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto descriptivo
 */
function showLoading(title = 'Procesando...', text = 'Por favor, espere mientras se procesa su solicitud.') {
    Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Cerrar cualquier alerta abierta
 */
function closeAlert() {
    Swal.close();
}

/**
 * Manejo de errores AJAX
 * @param {Object} xhr - Objeto XMLHttpRequest
 * @param {string} textStatus - Estado del texto
 * @param {string} errorThrown - Error lanzado
 */
function handleAjaxError(xhr, textStatus, errorThrown) {
    let title = 'Error de Comunicación';
    let message = 'Ha ocurrido un error al comunicarse con el servidor.';
    
    if (xhr.status === 422) {
        // Errores de validación
        title = 'Error de Validación';
        message = 'Por favor, revise los datos ingresados.';
        
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            const errorMessages = Object.values(errors).flat();
            message = errorMessages.join('\n');
        }
    } else if (xhr.status === 404) {
        title = 'Recurso No Encontrado';
        message = 'El recurso solicitado no existe.';
    } else if (xhr.status === 403) {
        title = 'Acceso Denegado';
        message = 'No tiene permisos para realizar esta acción.';
    } else if (xhr.status === 500) {
        title = 'Error del Servidor';
        message = 'Ha ocurrido un error interno en el servidor.';
    } else if (xhr.status === 0) {
        title = 'Error de Conexión';
        message = 'No se pudo conectar con el servidor. Verifique su conexión a internet.';
    }
    
    showError(title, message);
}

/**
 * Función para eliminar con confirmación
 * @param {string} url - URL para eliminar
 * @param {string} itemName - Nombre del elemento a eliminar
 * @param {Function} callback - Función a ejecutar después de eliminar
 */
function deleteWithConfirmation(url, itemName = '', callback = null) {
    confirmDelete('¿Eliminar elemento?', '', itemName).then((result) => {
        if (result.isConfirmed) {
            showLoading('Eliminando...', 'Por favor, espere.');
            
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    closeAlert();
                    showSuccess('Eliminado', 'El elemento ha sido eliminado correctamente.');
                    
                    if (callback && typeof callback === 'function') {
                        callback(response);
                    } else {
                        // Recargar la página por defecto
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    closeAlert();
                    handleAjaxError(xhr, textStatus, errorThrown);
                }
            });
        }
    });
}

/**
 * Función para enviar formulario con confirmación
 * @param {string} formId - ID del formulario
 * @param {string} confirmMessage - Mensaje de confirmación
 * @param {Function} callback - Función a ejecutar después del envío
 */
function submitFormWithConfirmation(formId, confirmMessage = '¿Está seguro de que desea enviar este formulario?', callback = null) {
    confirmAction('Confirmar envío', confirmMessage, 'question').then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById(formId);
            if (form) {
                showLoading('Enviando...', 'Por favor, espere.');
                form.submit();
            }
        }
    });
}

// Exportar funciones para uso global
window.SweetAlertHelper = {
    confirmDelete,
    confirmAction,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showInput,
    showLoading,
    closeAlert,
    handleAjaxError,
    deleteWithConfirmation,
    submitFormWithConfirmation
};
