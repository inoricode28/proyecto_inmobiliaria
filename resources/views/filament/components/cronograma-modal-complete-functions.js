// Función auxiliar para agregar cuotas a la tabla sin limpiarla
function appendCuotasToTable(cuotas) {
    console.log('📝 Agregando', cuotas.length, 'cuotas adicionales a la tabla');
    
    const cuotasTableBody = document.getElementById('cuotasTableBody');
    if (!cuotasTableBody) {
        console.error('❌ ERROR: No se encontró el elemento cuotasTableBody');
        return;
    }
    
    // Obtener tipos de cuota
    fetch('/api/tipos-cuota')
        .then(response => response.json())
        .then(tiposData => {
            if (tiposData.success && tiposData.data) {
                const tiposCuota = tiposData.data;
                console.log('✅ Tipos de cuota obtenidos para agregar:', tiposCuota.length);
                
                cuotas.forEach((cuota, index) => {
                    console.log(`Agregando cuota adicional ${index + 1}:`, cuota);
                    
                    const row = document.createElement('tr');
                    
                    // Fecha de pago
                    const fechaCell = document.createElement('td');
                    fechaCell.innerHTML = `<input type="date" class="form-control" value="${cuota.fecha_pago || ''}" name="cuotas[${cuotasTableBody.children.length}][fecha_pago]">`;
                    row.appendChild(fechaCell);
                    
                    // Monto
                    const montoCell = document.createElement('td');
                    montoCell.innerHTML = `<input type="number" class="form-control" value="${cuota.monto || 0}" step="0.01" name="cuotas[${cuotasTableBody.children.length}][monto]">`;
                    row.appendChild(montoCell);
                    
                    // Tipo de cuota
                    const tipoCell = document.createElement('td');
                    let tipoOptions = tiposCuota.map(tipo => 
                        `<option value="${tipo.id}" ${tipo.id == cuota.tipo_cuota_id ? 'selected' : ''}>${tipo.nombre}</option>`
                    ).join('');
                    tipoCell.innerHTML = `<select class="form-control" name="cuotas[${cuotasTableBody.children.length}][tipo_cuota_id]">${tipoOptions}</select>`;
                    row.appendChild(tipoCell);
                    
                    // Acciones
                    const accionesCell = document.createElement('td');
                    accionesCell.innerHTML = `<button type="button" class="btn btn-danger btn-sm" onclick="eliminarCuota(this)"><i class="fas fa-trash"></i></button>`;
                    row.appendChild(accionesCell);
                    
                    cuotasTableBody.appendChild(row);
                    console.log(`Cuota adicional ${index + 1} agregada a la tabla`);
                });
                
                // Mostrar la sección de cuotas
                const cuotasSection = document.getElementById('cuotasSection');
                if (cuotasSection) {
                    cuotasSection.style.display = 'block';
                    console.log('✅ Sección de cuotas mostrada');
                }
                
                console.log('✅ Cuotas adicionales agregadas correctamente');
            } else {
                console.error('❌ Error en la respuesta de tipos de cuota:', tiposData);
            }
        })
        .catch(error => {
            console.error('❌ Error al obtener tipos de cuota para agregar:', error);
        });
}

// Función para verificar y generar cuota por defecto cuando no hay cuotas
function checkAndGenerateDefaultCuota() {
    console.log('🔍 Verificando si generar cuota por defecto...');
    
    const cuotasTableBody = document.getElementById('cuotasTableBody');
    const hasExistingCuotas = cuotasTableBody && cuotasTableBody.children.length > 0;
    
    if (hasExistingCuotas) {
        console.log('ℹ️ Ya hay cuotas en la tabla, no se genera cuota por defecto');
        return;
    }
    
    // Obtener el monto total
    const montoTotalElement = document.getElementById('montoTotal');
    const montoTotal = montoTotalElement ? parseFloat(montoTotalElement.value) : 0;
    
    if (montoTotal > 0) {
        console.log('🔄 Generando cuota por defecto con monto:', montoTotal);
        generateDefaultCuota(montoTotal);
    } else {
        console.log('⚠️ No se puede generar cuota por defecto: monto total no válido');
    }
}