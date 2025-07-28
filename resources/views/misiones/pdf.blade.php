<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Misión - {{ $mision->nombre_clave ?? 'Sin Nombre Clave' }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 8px; color: #2c3e50; }
        .data-grid { display: grid; grid-template-columns: 150px 1fr; gap: 8px; margin-bottom: 5px; }
        .label { font-weight: bold; color: #34495e; }
        .value { color: #2c3e50; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Misión</h1>
        <p>Misión: {{ $mision->nombre_clave ?? 'N/A' }} | Estatus: {{ $mision->estatus }}</p>
    </div>

    <div class="section">
        <div class="section-title">Información General</div>
        <div class="data-grid">
            <div class="label">Tipo de Servicio:</div>
            <div class="value">{{ $mision->tipo_servicio }}</div>
            
            <div class="label">Ubicación:</div>
            <div class="value">{{ $mision->ubicacion }}</div>
            
            <div class="label">Fecha Inicio:</div>
            <div class="value">{{ $mision->fecha_inicio }}</div>
            
            <div class="label">Fecha Fin:</div>
            <div class="value">{{ $mision->fecha_fin }}</div>
            
            <div class="label">Nivel de Amenaza:</div>
            <div class="value">{{ $mision->nivel_amenaza }}</div>
            
            <div class="label">Cliente:</div>
            <div class="value">{{ $mision->cliente ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalles de Operación</div>
        <div class="data-grid">
            <div class="label">Nombre Clave:</div>
            <div class="value">{{ $mision->nombre_clave ?? 'N/A' }}</div>
            
            <div class="label">Tipo de Operación:</div>
            <div class="value">{{ $mision->tipo_operacion ?? 'N/A' }}</div>
            
            <div class="label">Número de Vehículos:</div>
            <div class="value">{{ $mision->num_vehiculos ?? '0' }}</div>
            
            <div class="label">Tipo de Vehículos:</div>
            <div class="value">
                @if(!empty($mision->tipo_vehiculos))
                    {{ implode(', ', $mision->tipo_vehiculos) }}
                @else
                    N/A
                @endif
            </div>
            
            <div class="label">Armados:</div>
            <div class="value">{{ $mision->armados ?? 'No' }}</div>
            
            <div class="label">Pasajeros:</div>
            <div class="value">{{ $mision->pasajeros ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Información Adicional</div>

        @if(!empty($mision->datos_hotel))
            <div style="margin-bottom: 15px;">
                <div><strong>Hotel:</strong></div>
                <div>Nombre: {{ $mision->datos_hotel['nombre'] ?? 'N/A' }}</div>
                <div>Dirección: {{ $mision->datos_hotel['direccion'] ?? 'N/A' }}</div>
                <div>Teléfono: {{ $mision->datos_hotel['telefono'] ?? 'N/A' }}</div>
            </div>
        @endif

        @if(!empty($mision->datos_aeropuerto))
            <div style="margin-bottom: 15px;">
                <div><strong>Aeropuerto:</strong></div>
                <div>Nombre: {{ $mision->datos_aeropuerto['nombre'] ?? 'N/A' }}</div>
                <div>Dirección: {{ $mision->datos_aeropuerto['direccion'] ?? 'N/A' }}</div>
                <div>Teléfono: {{ $mision->datos_aeropuerto['telefono'] ?? 'N/A' }}</div>
            </div>
        @endif

        @if(!empty($mision->datos_vuelo))
            <div style="margin-bottom: 15px;">
                <div><strong>Vuelo:</strong></div>
                <div>Fecha: {{ $mision->datos_vuelo['fecha'] ?? 'N/A' }}</div>
                <div>Hora: {{ $mision->datos_vuelo['hora'] ?? 'N/A' }}</div>
                <div>Número de Vuelo: {{ $mision->datos_vuelo['flight'] ?? 'N/A' }}</div>
                <div>Pasajeros: {{ $mision->datos_vuelo['pax'] ?? 'N/A' }}</div>
                <div>Evento: {{ $mision->datos_vuelo['event'] ?? 'N/A' }}</div>
                <div>Aeropuerto: {{ $mision->datos_vuelo['aeropuerto'] ?? 'N/A' }}</div>
            </div>
        @endif

        @if(!empty($mision->datos_hospital))
            <div style="margin-bottom: 15px;">
                <div><strong>Hospital:</strong></div>
                <div>Nombre: {{ $mision->datos_hospital['nombre'] ?? 'N/A' }}</div>
                <div>Dirección: {{ $mision->datos_hospital['direccion'] ?? 'N/A' }}</div>
                <div>Teléfono: {{ $mision->datos_hospital['telefono'] ?? 'N/A' }}</div>
            </div>
        @endif

        @if(!empty($mision->datos_embajada))
            <div style="margin-bottom: 15px;">
                <div><strong>Embajada:</strong></div>
                <div>Nombre: {{ $mision->datos_embajada['nombre'] ?? 'N/A' }}</div>
                <div>Dirección: {{ $mision->datos_embajada['direccion'] ?? 'N/A' }}</div>
                <div>Teléfono: {{ $mision->datos_embajada['telefono'] ?? 'N/A' }}</div>
            </div>
        @endif
    </div>

    @if(!empty($mision->agentes_id))
        <div class="section">
            <div class="section-title">Agentes Asignados</div>
            <ul>
                @foreach($mision->agentes_id as $agenteId)
                    @php $agente = App\Models\apiUser::find($agenteId); @endphp
                    <li>{{ $agente->name ?? 'Agente #'.$agenteId }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión de Misiones
    </div>
</body>
</html>