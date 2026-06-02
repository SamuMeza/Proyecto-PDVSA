<div class="page-header">
    <h1 class="page-title">Generar Reporte</h1>
</div>

<div class="page-card">
    <form id="generar-reporte-form">
        <div class="form-group">
            <label for="tipo_reporte">Tipo de Reporte</label>
            <select id="tipo_reporte" name="tipo_reporte" required>
                <option value="">Seleccione un tipo...</option>
                <option value="fallas">Fallas</option>
                <option value="cumplimiento">Cumplimiento</option>
                <option value="resumen-mensual">Resumen Mensual</option>
                <option value="tecnicos">Técnicos</option>
            </select>
        </div>

        <div id="filtros-container" class="filtros-container" style="display:none;">
            <h3>Filtros</h3>
            
            <!-- Filtros para Fallas -->
            <div id="filtros-fallas" class="filtro-group" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_falla_id">Tipo de Falla</label>
                        <select id="tipo_falla_id" data-filter="tipo_falla_id">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="zona_id">Zona</label>
                        <select id="zona_id" data-filter="zona_id">
                            <option value="">Todas</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="prioridad_id">Prioridad</label>
                        <select id="prioridad_id" data-filter="prioridad_id">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" data-filter="estado">
                            <option value="">Todos</option>
                            <option value="reportada">Reportada</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="cerrada">Cerrada</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="mantenedor_id">Técnico</label>
                        <select id="mantenedor_id" data-filter="mantenedor_id">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" id="fecha_desde" data-filter="fecha_desde">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha Hasta</label>
                        <input type="date" id="fecha_hasta" data-filter="fecha_hasta">
                    </div>
                </div>
            </div>

            <!-- Filtros para Cumplimiento -->
            <div id="filtros-cumplimiento" class="filtro-group" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="zona_id_cumplimiento">Zona</label>
                        <select id="zona_id_cumplimiento" data-filter="zona_id">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nivel_mantenimiento_id">Nivel de Mantenimiento</label>
                        <select id="nivel_mantenimiento_id" data-filter="nivel_mantenimiento_id">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="estado_cumplimiento">Estado</label>
                        <select id="estado_cumplimiento" data-filter="estado">
                            <option value="">Todos</option>
                            <option value="cerrada">Cerrada</option>
                            <option value="en_curso">En Curso</option>
                            <option value="planificada">Planificada</option>
                            <option value="suspendida">Suspendida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha_desde_cumplimiento">Fecha Desde</label>
                        <input type="date" id="fecha_desde_cumplimiento" data-filter="fecha_desde">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_hasta_cumplimiento">Fecha Hasta</label>
                        <input type="date" id="fecha_hasta_cumplimiento" data-filter="fecha_hasta">
                    </div>
                </div>
            </div>

            <!-- Filtros para Resumen Mensual -->
            <div id="filtros-resumen-mensual" class="filtro-group" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="mes_desde">Mes Desde</label>
                        <input type="month" id="mes_desde" data-filter="mes_desde">
                    </div>
                    <div class="form-group">
                        <label for="mes_hasta">Mes Hasta</label>
                        <input type="month" id="mes_hasta" data-filter="mes_hasta">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="zona_id_resumen">Zona</label>
                        <select id="zona_id_resumen" data-filter="zona_id">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nivel_mantenimiento_id_resumen">Nivel de Mantenimiento</label>
                        <select id="nivel_mantenimiento_id_resumen" data-filter="nivel_mantenimiento_id">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Filtros para Técnicos -->
            <div id="filtros-tecnicos" class="filtro-group" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="mantenedor_id_tecnico">Técnico</label>
                        <select id="mantenedor_id_tecnico" data-filter="mantenedor_id">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="zona_id_tecnico">Zona</label>
                        <select id="zona_id_tecnico" data-filter="zona_id">
                            <option value="">Todas</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="estado_tecnico">Estado</label>
                        <select id="estado_tecnico" data-filter="estado">
                            <option value="">Todos</option>
                            <option value="reportada">Reportada</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="cerrada">Cerrada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha_desde_tecnico">Fecha Desde</label>
                        <input type="date" id="fecha_desde_tecnico" data-filter="fecha_desde">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_hasta_tecnico">Fecha Hasta</label>
                        <input type="date" id="fecha_hasta_tecnico" data-filter="fecha_hasta">
                    </div>
                </div>
            </div>

            <div class="registros-info" id="registros-info">
                <span id="registros-count">0</span> registros encontrados
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="btn-generar" disabled>
                Generar Reporte PDF
            </button>
        </div>
    </form>
</div>

<div id="resultado-container" style="display:none; margin-top:1rem;">
    <div class="page-card">
        <h3>Reporte generado exitosamente</h3>
        <p id="resultado-mensaje"></p>
        <div class="form-actions">
            <a id="btn-descargar-pdf" href="#" class="btn btn-primary">Descargar PDF</a>
            <a id="btn-descargar-csv" href="#" class="btn btn-secondary">Descargar CSV</a>
            <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/historial" class="btn btn-secondary">Ver Historial</a>
        </div>
    </div>
</div>

<script src="<?= \App\Core\App::BASE_PATH ?>/public/assets/js/reportes.js"></script>
