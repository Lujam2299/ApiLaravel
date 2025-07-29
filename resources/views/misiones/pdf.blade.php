<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Misión - {{ $mision->nombre_clave ?? 'Sin Nombre Clave' }}</title>
    <style>
        /* Estilos base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        
        /* Contenedor principal */
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            padding: 30px;
        }
        
        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e1e1e1;
            position: relative;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .mission-title {
            font-size: 22px;
            color: #3498db;
            margin: 10px 0;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .status-active {
            background-color: #e3f7e8;
            color: #27ae60;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #f39c12;
        }
        
        .status-finished {
            background-color: #f5e6e8;
            color: #e74c3c;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 15px;
            color: #2c3e50;
            position: relative;
            padding-left: 15px;
        }
        
        .section-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            height: 18px;
            width: 5px;
            background-color: #3498db;
            border-radius: 3px;
        }
        
        /* Grid de datos */
        .data-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 10px;
            margin-bottom: 8px;
        }
        
        .label {
            font-weight: 500;
            color: #7f8c8d;
        }
        
        .value {
            color: #2c3e50;
            font-weight: 400;
        }
        
        /* Información de ubicaciones */
        .location-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        
        .location-title {
            font-weight: 600;
            color: #3498db;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        /* Lista de agentes */
        .agents-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .agent-badge {
            background: #e8f4fc;
            color: #2980b9;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        /* Pie de página */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e1e1e1;
            color: #95a5a6;
            font-size: 12px;
        }
        
        /* Elementos destacados */
        .highlight-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .threat-level {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .threat-high {
            background-color: #fde8e8;
            color: #e74c3c;
        }
        
        .threat-medium {
            background-color: #fff4e5;
            color: #f39c12;
        }
        
        .threat-low {
            background-color: #e8f7f0;
            color: #27ae60;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .data-grid {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            
            .label {
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte de Misión</h1>
            <div class="mission-title">{{ $mision->nombre_clave ?? 'N/A' }}</div>
            <div class="status-badge status-{{ strtolower($mision->estatus) }}">
                {{ $mision->estatus }}
            </div>
        </div>
        
        <div class="highlight-box">
            <div class="data-grid">
                <div class="label">Tipo de Servicio:</div>
                <div class="value">{{ $mision->tipo_servicio ?? 'N/A' }}</div>
                
                <div class="label">Cliente:</div>
                <div class="value">{{ $mision->cliente ?? 'N/A' }}</div>
                
                <div class="label">Nivel de Amenaza:</div>
                <div class="value">
                    <span class="threat-level threat-{{ strtolower($mision->nivel_amenaza) }}">
                        {{ $mision->nivel_amenaza ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Fechas y Ubicación</div>
            <div class="data-grid">
                <div class="label">Fecha de Inicio:</div>
                <div class="value">{{ $mision->fecha_inicio ?? 'N/A' }}</div>
                
                <div class="label">Fecha de Fin:</div>
                <div class="value">{{ $mision->fecha_fin ?? 'N/A' }}</div>
                
                <div class="label">Ubicación Principal:</div>
                <div class="value">{{ $mision->ubicacion ?? 'N/A' }}</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Detalles Operativos</div>
            <div class="data-grid">
                <div class="label">Tipo de Operación:</div>
                <div class="value">{{ $mision->tipo_operacion ?? 'N/A' }}</div>
                
                <div class="label">Vehículos:</div>
                <div class="value">
                    {{ $mision->num_vehiculos ?? '0' }} 
                    @if(!empty($mision->tipo_vehiculos))
                        ({{ implode(', ', $mision->tipo_vehiculos) }})
                    @endif
                </div>
                
                <div class="label">Armados:</div>
                <div class="value">{{ $mision->armados ? 'Sí' : 'No' }}</div>
                
                <div class="label">Pasajeros:</div>
                <div class="value">{{ $mision->pasajeros ?? 'N/A' }}</div>
            </div>
        </div>
        
        @if(!empty($mision->datos_hotel) || !empty($mision->datos_aeropuerto) || 
            !empty($mision->datos_vuelo) || !empty($mision->datos_hospital) || 
            !empty($mision->datos_embajada))
            <div class="section">
                <div class="section-title">Ubicaciones Específicas</div>
                
                @if(!empty($mision->datos_hotel))
                    <div class="location-card">
                        <div class="location-title">Hotel</div>
                        <div class="data-grid">
                            <div class="label">Nombre:</div>
                            <div class="value">{{ $mision->datos_hotel['nombre'] ?? 'N/A' }}</div>
                            
                            <div class="label">Dirección:</div>
                            <div class="value">{{ $mision->datos_hotel['direccion'] ?? 'N/A' }}</div>
                            
                            <div class="label">Teléfono:</div>
                            <div class="value">{{ $mision->datos_hotel['telefono'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
                
                @if(!empty($mision->datos_aeropuerto))
                    <div class="location-card">
                        <div class="location-title">Aeropuerto</div>
                        <div class="data-grid">
                            <div class="label">Nombre:</div>
                            <div class="value">{{ $mision->datos_aeropuerto['nombre'] ?? 'N/A' }}</div>
                            
                            <div class="label">Dirección:</div>
                            <div class="value">{{ $mision->datos_aeropuerto['direccion'] ?? 'N/A' }}</div>
                            
                            <div class="label">Teléfono:</div>
                            <div class="value">{{ $mision->datos_aeropuerto['telefono'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
                
                @if(!empty($mision->datos_vuelo))
                    <div class="location-card">
                        <div class="location-title">Detalles de Vuelo</div>
                        <div class="data-grid">
                            <div class="label">Número de Vuelo:</div>
                            <div class="value">{{ $mision->datos_vuelo['flight'] ?? 'N/A' }}</div>
                            
                            <div class="label">Fecha y Hora:</div>
                            <div class="value">
                                {{ $mision->datos_vuelo['fecha'] ?? 'N/A' }} 
                                a las {{ $mision->datos_vuelo['hora'] ?? 'N/A' }}
                            </div>
                            
                            <div class="label">Pasajeros:</div>
                            <div class="value">{{ $mision->datos_vuelo['pax'] ?? 'N/A' }}</div>
                            
                            <div class="label">Aeropuerto:</div>
                            <div class="value">{{ $mision->datos_vuelo['aeropuerto'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
                
                @if(!empty($mision->datos_hospital))
                    <div class="location-card">
                        <div class="location-title">Hospital</div>
                        <div class="data-grid">
                            <div class="label">Nombre:</div>
                            <div class="value">{{ $mision->datos_hospital['nombre'] ?? 'N/A' }}</div>
                            
                            <div class="label">Dirección:</div>
                            <div class="value">{{ $mision->datos_hospital['direccion'] ?? 'N/A' }}</div>
                            
                            <div class="label">Teléfono:</div>
                            <div class="value">{{ $mision->datos_hospital['telefono'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
                
                @if(!empty($mision->datos_embajada))
                    <div class="location-card">
                        <div class="location-title">Embajada</div>
                        <div class="data-grid">
                            <div class="label">Nombre:</div>
                            <div class="value">{{ $mision->datos_embajada['nombre'] ?? 'N/A' }}</div>
                            
                            <div class="label">Dirección:</div>
                            <div class="value">{{ $mision->datos_embajada['direccion'] ?? 'N/A' }}</div>
                            
                            <div class="label">Teléfono:</div>
                            <div class="value">{{ $mision->datos_embajada['telefono'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        
        @if(!empty($mision->agentes_id))
            <div class="section">
                <div class="section-title">Equipo Asignado</div>
                <div class="agents-list">
                    @foreach($mision->agentes_id as $agenteId)
                        @php $agente = App\Models\apiUser::find($agenteId); @endphp
                        <div class="agent-badge">{{ $agente->name ?? 'Agente #'.$agenteId }}</div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="footer">
            Documento generado el {{ now()->format('d/m/Y g:i A') }} | Sistema de Gestión de Misiones
        </div>
    </div>
</body>
</html>