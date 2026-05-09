{{-- Botón de geolocalización para filtros --}}
<button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="getUserLocation()" title="Usar mi ubicación actual">
    <i class="bi bi-crosshair me-1"></i>Usar mi ubicación
</button>

<script>
// Función global de geolocalización (adaptada para funcionar como en conciertos)
function getUserLocation() {
    const button = document.querySelector('.btn-outline-primary');
    const originalContent = button.innerHTML;
    
    // Mostrar estado de carga
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Obteniendo ubicación...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            // Success
            async function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                try {
                    // Usar Nominatim (OpenStreetMap) para reverse geocoding
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&accept-language=es`);
                    const data = await response.json();
                    
                    if (data && data.address) {
                        // Extraer información de España
                        const address = data.address;
                        let community = '';
                        let province = '';
                        
                        // Mapeo de comunidades autónomas
                        const communityMapping = {
                            'Andalucía': 'Andalucía',
                            'Andalusia': 'Andalucía',
                            'Aragón': 'Aragón',
                            'Aragon': 'Aragón',
                            'Principado de Asturias': 'Principado de Asturias',
                            'Asturias': 'Principado de Asturias',
                            'Islas Baleares': 'Islas Baleares',
                            'Balearic Islands': 'Islas Baleares',
                            'Canarias': 'Canarias',
                            'Canary Islands': 'Canarias',
                            'Cantabria': 'Cantabria',
                            'Castilla-La Mancha': 'Castilla-La Mancha',
                            'Castile-La Mancha': 'Castilla-La Mancha',
                            'Castilla y León': 'Castilla y León',
                            'Castile and León': 'Castilla y León',
                            'Cataluña': 'Cataluña',
                            'Catalonia': 'Cataluña',
                            'Comunidad Valenciana': 'Comunidad Valenciana',
                            'Valencian Community': 'Comunidad Valenciana',
                            'Extremadura': 'Extremadura',
                            'Galicia': 'Galicia',
                            'La Rioja': 'La Rioja',
                            'Rioja': 'La Rioja',
                            'Madrid': 'Comunidad de Madrid',
                            'Community of Madrid': 'Comunidad de Madrid',
                            'Navarra': 'Navarra',
                            'Navarre': 'Navarra',
                            'País Vasco': 'País Vasco',
                            'Basque Country': 'País Vasco',
                            'Euskadi': 'País Vasco'
                        };
                        
                        // Buscar comunidad
                        for (const [key, value] of Object.entries(communityMapping)) {
                            if (address.state === key || address.region === key) {
                                community = value;
                                break;
                            }
                        }
                        
                        // Provincia (generalmente es el campo county o city)
                        province = address.county || address.city || address.town || '';
                        
                        // Actualizar los filtros según la página actual
                        updateLocationFilters(community, province);
                        
                        // Mostrar éxito
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-check-circle me-1"></i>¡Ubicación encontrada!';
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-success');
                        
                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.classList.remove('btn-success');
                            button.classList.add('btn-outline-primary');
                        }, 2000);
                        
                    } else {
                        throw new Error('No se pudo determinar la ubicación');
                    }
                } catch (error) {
                    console.error('Error en geocoding:', error);
                    showLocationError(button, originalContent);
                }
            },
            // Error
            function(error) {
                console.error('Error de geolocalización:', error);
                showLocationError(button, originalContent);
            },
            // Opciones
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutos
            }
        );
    } else {
        alert('Tu navegador no soporta geolocalización');
        button.innerHTML = originalContent;
        button.disabled = false;
    }
}

function showLocationError(button, originalContent) {
    button.disabled = false;
    button.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Error';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-danger');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('btn-danger');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

function updateLocationFilters(community, province) {
    // Detectar en qué página estamos y actualizar los filtros correspondientes
    const url = window.location.pathname;
    
    if (url.includes('/musicians')) {
        // Página de músicos
        const communitySelect = document.getElementById('filterCommunity');
        const provinceSelect = document.getElementById('filterProvince');
        
        if (communitySelect) {
            communitySelect.value = community;
            updateProvinces(community, 'filterProvince', province);
            
            // Disparar el evento change para recargar la página
            communitySelect.dispatchEvent(new Event('change'));
        }
    } else if (url.includes('/merch')) {
        // Página de merchandising
        const radios = document.querySelectorAll('input[name="community"]');
        radios.forEach(radio => {
            if (radio.value === community) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
        
        // Actualizar provincia si existe
        if (province) {
            const provinceRadios = document.querySelectorAll('input[name="province"]');
            provinceRadios.forEach(radio => {
                if (radio.value === province) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        }
    } else if (url.includes('/radio')) {
        // Página de radio
        const communitySelect = document.getElementById('communitySelect');
        const provinceSelect = document.getElementById('provinceSelect');
        
        if (communitySelect) {
            communitySelect.value = community;
            onCommunityChange(community);
            
            if (province && provinceSelect) {
                setTimeout(() => {
                    provinceSelect.value = province;
                    onProvinceChange(province);
                }, 500);
            }
        }
    }
}
</script>
