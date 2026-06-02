document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('generar-reporte-form');
    const tipoReporteSelect = document.getElementById('tipo_reporte');
    const filtrosContainer = document.getElementById('filtros-container');
    const btnGenerar = document.getElementById('btn-generar');
    const registrosCount = document.getElementById('registros-count');
    const resultadoContainer = document.getElementById('resultado-container');

    // Ocultar todos los grupos de filtros
    function ocultarFiltros() {
        document.querySelectorAll('.filtro-group').forEach(el => {
            el.style.display = 'none';
        });
    }

    // Mostrar filtros según el tipo seleccionado
    function mostrarFiltros(tipo) {
        ocultarFiltros();
        if (tipo) {
            filtrosContainer.style.display = 'block';
            const filtroGroup = document.getElementById('filtros-' + tipo);
            if (filtroGroup) {
                filtroGroup.style.display = 'block';
            }
            cargarDatosFiltros(tipo);
            contarRegistros();
        } else {
            filtrosContainer.style.display = 'none';
            btnGenerar.disabled = true;
        }
    }

    // Cargar datos para los selects de filtros
    async function cargarDatosFiltros(tipo) {
        // Aquí se harían llamadas AJAX para cargar zonas, técnicos, etc.
        // Por ahora usamos datos de ejemplo o vacíos
        console.log('Cargando datos para tipo:', tipo);
    }

    // Construir JSON de filtros desde el formulario
    function construirFiltros() {
        const filtros = {};
        document.querySelectorAll('[data-filter]').forEach(el => {
            const key = el.getAttribute('data-filter');
            const value = el.value;
            if (value) {
                filtros[key] = value;
            }
        });
        return filtros;
    }

    // Contar registros con los filtros actuales
    async function contarRegistros() {
        const tipo = tipoReporteSelect.value;
        const filtros = construirFiltros();
        
        if (!tipo) {
            registrosCount.textContent = '0';
            btnGenerar.disabled = true;
            return;
        }

        try {
            const formData = new FormData();
            formData.append('tipo_reporte', tipo);
            formData.append('filtros', JSON.stringify(filtros));
            
            const response = await fetch(BASE_PATH + '/reportes/contar', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const data = await response.json();
                registrosCount.textContent = data.count || 0;
                btnGenerar.disabled = (data.count || 0) === 0;
            }
        } catch (error) {
            console.error('Error al contar registros:', error);
            registrosCount.textContent = '0';
            btnGenerar.disabled = true;
        }
    }

    // Event listeners
    tipoReporteSelect.addEventListener('change', function() {
        mostrarFiltros(this.value);
    });

    // Contar registros al cambiar cualquier filtro
    document.querySelectorAll('[data-filter]').forEach(el => {
        el.addEventListener('change', contarRegistros);
        el.addEventListener('input', contarRegistros);
    });

    // Enviar formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const tipo = tipoReporteSelect.value;
        const filtros = construirFiltros();
        
        if (!tipo) {
            alert('Seleccione un tipo de reporte');
            return;
        }

        btnGenerar.disabled = true;
        btnGenerar.textContent = 'Generando...';

        try {
            const formData = new FormData();
            formData.append('tipo_reporte', tipo);
            formData.append('filtros', JSON.stringify(filtros));
            
            const response = await fetch(BASE_PATH + '/reportes/generar', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.ok) {
                resultadoContainer.style.display = 'block';
                document.getElementById('resultado-mensaje').textContent = 
                    `Reporte generado con ${data.registros} registros.`;
                document.getElementById('btn-descargar-pdf').href = 
                    BASE_PATH + '/reportes/descargar/' + data.reporte_id + '?formato=pdf';
                document.getElementById('btn-descargar-csv').href = 
                    BASE_PATH + '/reportes/descargar/' + data.reporte_id + '?formato=csv';
            } else {
                alert(data.error || 'Error al generar el reporte');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar el reporte');
        } finally {
            btnGenerar.disabled = false;
            btnGenerar.textContent = 'Generar Reporte PDF';
        }
    });
});
