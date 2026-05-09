# Cambios Implementados - Pulse Project

## 1. ✅ Base de Datos - SQLite
- **Cambio**: Migrado a SQLite desde MySQL
- **Configuración**: `.env`
  - `DB_CONNECTION=sqlite`
  - `DB_DATABASE=database/database.sqlite`
- **Ubicación de BD**: `database/database.sqlite`

### Para inicializar o resetear la base de datos:
```bash
php artisan migrate:fresh --seed
```

---

## 2. ✅ Gráfico de Top 20 Artistas con Actualizaciones Diarias

### Modelos y Estructura:
- **Modelo nuevo**: `App\Models\ArtistPlayCount`
  - Almacena reproducciones diarias de cada artista
  - Se actualiza automáticamente a las 00:00

### Migraciones:
- `2026_04_27_142724_create_artist_play_counts_table`

### Jobs:
- **Job**: `App\Jobs\UpdateArtistPlayCounts`
  - Se dispara diariamente a las 00:00 automáticamente
  - Calcula el total de reproducciones por artista

### Comando Artisan:
```bash
# Ejecutar manualmente (útil para testing):
php artisan musicians:update-top-plays

# En producción, se ejecuta automáticamente a las 00:00 via Schedule
php artisan schedule:run
```

### Programación (routes/console.php):
```php
Schedule::command('musicians:update-top-plays')->dailyAt('00:00');
```

### Vista y Gráfico:
- **Ruta**: `/top-musicos`
- **Vista**: `resources/views/top-musicians/index.blade.php`
- **Gráfico**: Chart.js con datos en tiempo real
- **Endpoint API**: `/top-musicos/data` (retorna JSON)

---

## 3. ✅ Filtro de Conciertos por Proximidad - Leaflet

### Características:
- **Mapa interactivo** con Leaflet en `resources/views/concerts/index.blade.php`
- **Localización del usuario** automática
- **Filtros disponibles**:
  - Por radio (50 km, 100 km, 200 km, 500 km)
  - Por comunidad autónoma
  - Por provincia
  - Por género musical
  - Por precio máximo
  - Ordenar por: fecha, precio, proximidad

### Endpoint API:
```
GET /conciertos/map-data?lat={latitude}&lng={longitude}&radius={km}
```

### Funcionalidad:
- Detecta ubicación del usuario (con permiso)
- Muestra conciertos cercanos en el mapa
- Calcula distancia usando Haversine
- Marcadores personalizados para conciertos

---

## 4. ✅ Landing Page - Imagen Publico

- **Ubicación**: `public/images/Publico.png`
- **Uso**: Sección Hero en `resources/views/home.blade.php`
- **URL en vistas**: `{{ asset('images/Publico.png') }}`

---

## 5. ✅ Almacenamiento de Archivos - Configuración

### Estructura:
- **Recursos del sistema** (imágenes, CSS, etc.): `public/`
- **Archivos de usuarios** (música, imágenes, etc.): `storage/app/public/`

### Discos Disponibles:
```php
// Archivos privados
Storage::disk('local')->put('file.txt', 'contents');

// Archivos públicos de usuarios (con acceso web)
Storage::disk('public')->put('music/song.mp3', $contents);

// Recursos estáticos del sistema
Storage::disk('resources')->put('image.png', $contents);
```

### Link Simbólico:
- Ejecutado automáticamente: `php artisan storage:link`
- Crea acceso web a `storage/app/public` en `public/storage`
- **URL para acceder**: `/storage/{ruta-del-archivo}`

### Ejemplo de Uso en Código:
```php
// Guardar archivo de usuario
$path = $request->file('music')->store('music', 'public');

// Acceder en vista
<audio src="{{ asset('storage/' . $path) }}"></audio>

// O usando Storage facade
<audio src="{{ Storage::disk('public')->url($path) }}"></audio>
```

---

## 6. ✅ Música y Canciones Reales - Sistema Completado

### Estructura de Archivos:
- **Ubicación**: `public/music/`
- **Total de archivos**: 113 MP3 + 18 imágenes de portada
- **Organización original**: Preservada en carpetas de álbumes
- **Ejemplo**: `music/RAHE-12/2007 - In Rainbows (Special Edition)/CD1/10 - Videotape.mp3`

### Base de Datos:
- **Total de canciones**: 120 registros en tabla `songs`
- **Distribución**: 2-3 canciones por artista (48 artistas totales)
- **Play counts**: Asignados aleatoriamente (100-10,000 reproducciones)
- **Rutas normalizadas**: Todas usan forward slashes (`/`) para compatibilidad web

### Comandos de Gestión:

**Poblar canciones desde carpeta music:**
```bash
php artisan music:populate-songs
```
- Elimina canciones dummy existentes
- Distribuye MP3s entre artistas
- Genera play_count aleatorios
- Actualiza estadísticas (total_plays)

**Normalizar rutas de canciones:**
```bash
php artisan music:normalize-paths
```
- Convierte `\` a `/` en todas las rutas
- Hace las URLs accesibles desde web
- Ejemplo: `http://pulse.project/music/...`

### Acceso a Canciones en Vistas:

**En Blade:**
```blade
<audio controls>
    <source src="{{ asset($song->file_path) }}" type="audio/mpeg">
</audio>
```

**En componentes de player:**
```blade
<audio id="player" src="{{ asset($song->file_path) }}"></audio>
```

### Estadísticas:
- **Canciones por artista**: 2-3 canciones
- **Total de reproducciones**: Variable según asignación aleatoria
- **Géneros**: Variados (Rock, Jazz, Flamenco, Blues, Country, etc.)

---

## 7. 📋 Resumen de Cambios de Archivos

### Archivos Modificados:
1. `.env` - Configuración de SQLite y filesystem
2. `config/filesystems.php` - Discos de almacenamiento
3. `config/database.php` - Base de datos
4. `app/Http/Controllers/TopMusiciansController.php` - Datos actualizados diariamente
5. `resources/views/home.blade.php` - Imagen Publico.png en landing

### Archivos Creados:
1. `app/Models/ArtistPlayCount.php` - Modelo de estadísticas
2. `app/Jobs/UpdateArtistPlayCounts.php` - Job para actualizar datos
3. `app/Console/Commands/UpdateTopPlaysCommand.php` - Comando Artisan
4. `app/Console/Commands/PopulateSongsCommand.php` - Población de canciones
5. `app/Console/Commands/NormalizeSongPaths.php` - Normalización de rutas
6. `database/migrations/2026_04_27_142724_create_artist_play_counts_table.php` - Migración
7. `public/music/` - Carpeta con 113 MP3s y 18 imágenes

### Archivos Sin Cambios (Ya Implementados):
- `app/Http/Controllers/ConcertController.php` - Leaflet ya estaba
- `resources/views/concerts/index.blade.php` - Leaflet ya estaba
- `resources/views/top-musicians/index.blade.php` - Gráfico ya estaba

---

## 8. ⚙️ Configuración de Scheduler en Producción

Para que el comando se ejecute automáticamente a las 00:00 en un servidor:

### Opción 1: Cron (Recomendado)
```bash
# Agregar al crontab:
* * * * * cd /ruta/a/pulseProject && php artisan schedule:run >> /dev/null 2>&1
```

### Opción 2: Alternativa Windows (Task Scheduler)
```batch
cd C:\xampp\htdocs\pulseProject && php artisan schedule:run
```

---

## 9. 🧪 Testing y Verificación

### Verificar que todo está funcionando:

```bash
# 1. Verificar base de datos SQLite
ls -la database/database.sqlite

# 2. Probar comando manualmente
php artisan musicians:update-top-plays

# 3. Verificar que los datos se guardaron
php artisan tinker
>>> ArtistPlayCount::latest()->first()
>>> Song::count()
>>> Song::with('musicianProfile')->first()

# 4. Verificar endpoints API
curl "http://localhost/top-musicos/data"
curl "http://localhost/conciertos/map-data?lat=40.4168&lng=-3.7038&radius=100"

# 5. Verificar acceso a canciones
curl -I "http://localhost/music/RAHE-12/2007 - In Rainbows (Special Edition)/CD1/10 - Videotape.mp3"
```

---

## 📝 Notas Importantes

1. **SQLite**: Es perfecto para desarrollo local. Para producción con múltiples usuarios, considera MySQL/PostgreSQL.
2. **Storage**: Los archivos de usuarios se guardan en `storage/app/public`, accesibles como `/storage/{filename}`
3. **Scheduler**: Asegúrate de que el cron/scheduler esté corriendo en producción
4. **Permisos**: La carpeta `storage` y `public/music` necesitan permisos de escritura (775/777)
5. **Gráfico**: Se actualiza automáticamente cada 24 horas a las 00:00
6. **Música**: 113 canciones reales distribuidas entre 48 artistas con rutas accesibles
7. **Play Counts**: Cada canción tiene un contador aleatorio; se actualiza cuando se toca en radio

---

## 🎵 Estructura de Música Final

```
public/
├── music/
│   ├── KRD-10/
│   │   └── 2007 - Untitled (Deluxe Edition)/
│   │       └── [canciones MP3]
│   ├── LIP-04/
│   │   └── 2023 - DIAMONDS/
│   │       └── [canciones MP3]
│   ├── LMGF-21/
│   │   └── 2005 - Mexico En La Piel/
│   │       └── [canciones MP3]
│   ├── QU-09/
│   │   └── 2005 - Lullabies To Paralyze/
│   │       └── [canciones MP3]
│   ├── RAHE-12/
│   │   └── 2007 - In Rainbows (Special Edition)/
│   │       ├── CD1/
│   │       └── CD2/
│   ├── SKI-14/
│   │   └── 2010 - The Sun Comes Out/
│   │       └── [canciones MP3]
│   └── THOFF-11/
│       └── 2008 - Rise And Fall, Rage And Grace/
│           └── [canciones MP3]
```

