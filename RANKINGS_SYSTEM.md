# Sistema de Rankings en Tiempo Real - Pulse Project

## 📊 Arquitectura del Sistema

### 1️⃣ Flujo de Reproducciones
```
Usuario hace play en canción
    ↓
POST /api/songs/{song}/play
    ↓
Registra en song_play_logs
    ↓
Incrementa play_count de canción
    ↓
Se suma automáticamente a total de artista
```

### 2️⃣ Actualización de Rankings
```
Cada 15 minutos (scheduler):
    ↓
Comando: rankings:recalculate
    ↓
Calcula SUM(play_count) de cada artista
    ↓
Ordena por reproducciones (mayor a menor)
    ↓
Almacena en tabla musician_rankings
    ↓
Cachea en memoria (15 minutos)
    ↓
Frontend consulta caché (instantáneo)
```

---

## 🎯 Endpoints API

### Registrar Reproducción
```http
POST /api/songs/{song}/play
Content-Type: application/json

Respuesta:
{
    "success": true,
    "play_count": 8213
}
```

**Uso en frontend:**
```javascript
// Al hacer play en una canción
fetch('/api/songs/1/play', { method: 'POST' })
    .then(res => res.json())
    .then(data => {
        console.log('Reproducciones totales:', data.play_count);
    });
```

### Obtener Top 20 (JSON)
```http
GET /top-musicos/data

Respuesta:
{
    "musicians": [
        {
            "id": 14,
            "name": "Timple Sessions",
            "plays": 28107,
            "genre": "Traditional Canary",
            "city": "Las Palmas",
            "image": null
        },
        ...
    ]
}
```

---

## 📋 Tablas Involucradas

### `song_play_logs`
Registra cada reproducción (corta vida, para análisis)
```sql
id | song_id | played_at
1  | 5       | 2026-04-28 15:30:45
2  | 5       | 2026-04-28 15:32:10
3  | 12      | 2026-04-28 15:33:22
```

### `musician_rankings`
Rankings en caché (actualizado cada 15 min)
```sql
id | musician_profile_id | total_plays | rank | calculated_at
1  | 14                  | 28107       | 1    | 2026-04-28 15:45:00
2  | 13                  | 25430       | 2    | 2026-04-28 15:45:00
```

### `song_play_count` (en tabla `songs`)
Contador de reproducciones por canción
```sql
id | title              | play_count
1  | "10 - Videotape"   | 8212
2  | "06 - Up On..."    | 2105
```

---

## ⚙️ Comandos Disponibles

### Recalcular Rankings (Manual)
```bash
php artisan rankings:recalculate

# Output:
# 📊 Recalculando rankings de artistas...
# ✅ Rankings recalculados y cacheados
# 🏆 Top 1: Timple Sessions - 28107 reproducciones
```

### Generar Conciertos (Manual)
```bash
php artisan concerts:generate

# Output:
# 🎤 Generando conciertos para artistas...
# ✅ Conciertos generados: 89
# 🎵 Total de conciertos: 92
```

---

## 🔄 Scheduler (Automático)

### Configuración en `routes/console.php`:
```php
// Recalcular rankings cada 15 minutos
Schedule::command('rankings:recalculate')->everyFifteenMinutes();

// Actualización diaria a las 00:00
Schedule::command('musicians:update-top-plays')->dailyAt('00:00');
```

### Para que funcione en Producción:
```bash
# Agregar al crontab:
* * * * * cd /ruta/pulseProject && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Vista Top Músicos

### URL: `/top-musicos`

**Características:**
- ✅ Gráfico horizontal con Chart.js (top 20)
- ✅ Tabla ordenada de mayor a menor reproducciones
- ✅ Datos actualizados cada 15 minutos
- ✅ Badges para top 3 artistas
- ✅ Información: género, ciudad, reproducciones

### Flujo de Datos:
```
Vista carga
    ↓
GET /top-musicos → Controller retorna musicians
    ↓
JavaScript hace fetch a /top-musicos/data
    ↓
GET /top-musicos/data → Retorna JSON del caché
    ↓
Chart.js renderiza gráfico
    ↓
Tabla se llena con @foreach
```

---

## 🎤 Conciertos Generados

### Total: 92 conciertos para 48 artistas

**Características:**
- Ubicaciones reales en España
- Fechas futuras (1-90 días)
- Coordenadas GPS correctas
- Precios: €15-50
- Capacidades: 200-1000

### Ubicaciones Incluidas:
```
Barcelona, Madrid, Valencia, Sevilla, Bilbao,
Málaga, Alicante, Córdoba, Valladolid, Zaragoza,
Gijón, Salamanca, Murcia, Palma, Toledo,
Vitoria, Girona, Santiago de Compostela
```

### Ver Conciertos:
```
URL: /conciertos
Filtros:
  - Proximidad con mapa (Leaflet)
  - Radio: 50/100/200/500 km
  - Comunidad autónoma
  - Provincia
  - Precio máximo
```

---

## 📈 Ejemplo: Simular Reproducciones

### 1. Obtener canción
```bash
php artisan tinker
>>> $song = Song::first();
>>> $song->id // = 1
```

### 2. Registrar reproducciones manualmente
```bash
# Opción A: Mediante comando (desarrollo)
for i in {1..50}; do
    curl -X POST "http://pulse.project/api/songs/1/play"
done

# Opción B: En Tinker
>>> for ($i = 0; $i < 50; $i++) { 
    SongPlayLog::create(['song_id' => 1]); 
    Song::find(1)->increment('play_count');
}
```

### 3. Recalcular y verificar
```bash
php artisan rankings:recalculate

# Ver en BD
php artisan tinker
>>> MusicianRanking::orderBy('rank')->first()
>>> Song::find(1)->play_count
```

---

## 🔍 Verificación Rápida

```bash
# 1. Contar tablas
php artisan tinker
>>> SongPlayLog::count()           # Logs de reproducciones
>>> MusicianRanking::count()       # Rankings actuales
>>> Concert::count()               # Conciertos: 92
>>> Song::count()                  # Canciones: 120

# 2. Ver top 3
>>> MusicianRanking::orderBy('rank')->limit(3)->get()

# 3. Ver caché
>>> Cache::get('top_musicians_ranking')

# 4. Ver conciertos próximos
>>> Concert::where('date', '>=', now())->count()
```

---

## 🐛 Troubleshooting

**Problema: No veo gráfico en /top-musicos**
- Solución: Verifica que `/top-musicos/data` retorna JSON válido
- ```bash
  curl "http://pulse.project/top-musicos/data"
  ```

**Problema: Los rankings no se actualizan**
- Solución: Ejecuta manualmente `php artisan rankings:recalculate`
- Verifica que el scheduler esté corriendo: `php artisan schedule:work`

**Problema: Las reproducciones no se guardan**
- Solución: Verifica que la tabla `song_play_logs` exista
- ```bash
  php artisan migrate
  ```

---

## 📝 Notas Importantes

1. **Cache**: Se actualiza cada 15 minutos automáticamente
2. **Reproducciones**: Cada play se registra y suma al total del artista
3. **Rankings**: Basados en SUM(play_count) de todas sus canciones
4. **Conciertos**: 92 generados con coordenadas GPS reales
5. **Gráfico**: Requiere Chart.js (ya incluido)
6. **Scheduler**: Debe estar activo en producción
