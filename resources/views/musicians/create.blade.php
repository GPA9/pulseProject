<x-app-layout>
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-person-plus me-2 text-primary"></i>Crear Perfil de
                            Artista</h4>
                        <form action="{{ route('musicians.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="stage_name"
                                    class="form-label text-muted small text-uppercase fw-bold">Nombre artístico</label>
                                <input type="text" class="form-control bg-black border-secondary text-light"
                                    id="stage_name" name="stage_name" value="{{ old('stage_name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="genre" class="form-label text-muted small text-uppercase fw-bold">Género
                                    musical</label>
                                <input type="text" class="form-control bg-black border-secondary text-light" id="genre"
                                    name="genre" value="{{ old('genre') }}" placeholder="Ej: Rock, Jazz, Indie"
                                    required>
                            </div>

                            {{-- Ubicación --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Comunidad
                                    Autónoma</label>
                                <select class="form-select bg-black border-secondary text-light"
                                    name="autonomous_community" id="createCommunity"
                                    onchange="updateProvinces(this.value, 'createProvince', null)">
                                    <option value="">— Selecciona Comunidad —</option>
                                    @foreach($communities as $community => $provinces)
                                        <option value="{{ $community }}" {{ old('autonomous_community') === $community ? 'selected' : '' }}>
                                            {{ $community }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Provincia <span
                                        class="text-muted">(Opcional)</span></label>
                                <select class="form-select bg-black border-secondary text-light" name="province"
                                    id="createProvince">
                                    <option value="">— Selecciona Provincia —</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="city"
                                    class="form-label text-muted small text-uppercase fw-bold">Ciudad</label>
                                <input type="text" class="form-control bg-black border-secondary text-light" id="city"
                                    name="city" value="{{ old('city') }}" placeholder="Ej: Barcelona" required>
                            </div>

                            <div class="mb-3">
                                <label for="bio"
                                    class="form-label text-muted small text-uppercase fw-bold">Biografía</label>
                                <textarea class="form-control bg-black border-secondary text-light" id="bio" name="bio"
                                    rows="4">{{ old('bio') }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="form-label text-muted small text-uppercase fw-bold">Imagen de
                                    perfil</label>
                                <input type="file" class="form-control bg-black border-secondary text-light" id="image"
                                    name="image" accept="image/*">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Crear Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const communitiesMap = @json($communities);

        function updateProvinces(community, provinceSelectId, selectedProvince) {
            const sel = document.getElementById(provinceSelectId);
            sel.innerHTML = '<option value="">— Selecciona Provincia —</option>';
            if (community && communitiesMap[community]) {
                communitiesMap[community].forEach(function (prov) {
                    const opt = document.createElement('option');
                    opt.value = prov;
                    opt.textContent = prov;
                    if (selectedProvince && prov === selectedProvince) opt.selected = true;
                    sel.appendChild(opt);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const community = document.getElementById('createCommunity').value;
            if (community) updateProvinces(community, 'createProvince', "{{ old('province') }}");
        });
    </script>
</x-app-layout>