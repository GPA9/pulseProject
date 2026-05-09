<x-app-layout>
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-person-gear me-2 text-info"></i>Editar Perfil de Artista</h4>
                        <form action="{{ route('musicians.update', $musician) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="stage_name" class="form-label text-muted small text-uppercase fw-bold">Nombre artístico</label>
                                <input type="text" class="form-control bg-black border-secondary text-light"
                                    id="stage_name" name="stage_name"
                                    value="{{ old('stage_name', $musician->stage_name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="genre" class="form-label text-muted small text-uppercase fw-bold">Género musical</label>
                                <input type="text" class="form-control bg-black border-secondary text-light"
                                    id="genre" name="genre"
                                    value="{{ old('genre', $musician->genre) }}" required>
                            </div>

                            {{-- Ubicación --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Comunidad Autónoma</label>
                                <select class="form-select bg-black border-secondary text-light"
                                    name="autonomous_community" id="editCommunity"
                                    onchange="updateProvinces(this.value, 'editProvince', null)">
                                    <option value="">— Selecciona Comunidad —</option>
                                    @foreach($communities as $community => $provinces)
                                        <option value="{{ $community }}"
                                            {{ old('autonomous_community', $musician->autonomous_community) === $community ? 'selected' : '' }}>
                                            {{ $community }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Provincia <span class="text-muted">(Opcional)</span></label>
                                <select class="form-select bg-black border-secondary text-light"
                                    name="province" id="editProvince">
                                    <option value="">— Selecciona Provincia —</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label text-muted small text-uppercase fw-bold">Ciudad</label>
                                <input type="text" class="form-control bg-black border-secondary text-light"
                                    id="city" name="city"
                                    value="{{ old('city', $musician->city) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label text-muted small text-uppercase fw-bold">Biografía</label>
                                <textarea class="form-control bg-black border-secondary text-light"
                                    id="bio" name="bio" rows="4">{{ old('bio', $musician->bio) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="form-label text-muted small text-uppercase fw-bold">Imagen de perfil</label>
                                @if($musician->image_path)
                                    <div class="mb-2">
                                        @if(file_exists(public_path('images/band-logos/' . $musician->image_path)))
                                            <img src="{{ asset('images/band-logos/' . $musician->image_path) }}"
                                        @else
                                            <img src="{{ asset('storage/' . $musician->image_path) }}"
                                        @endif
                                            alt="Imagen actual" class="rounded-circle"
                                            style="width:60px;height:60px;object-fit:cover;">
                                        <span class="text-muted small ms-2">Imagen actual</span>
                                    </div>
                                @endif
                                <input type="file" class="form-control bg-black border-secondary text-light"
                                    id="image" name="image" accept="image/*">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Cambios
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
            const community = document.getElementById('editCommunity').value;
            const currentProvince = "{{ old('province', $musician->province) }}";
            if (community) {
                updateProvinces(community, 'editProvince', currentProvince);
                document.getElementById('editProvince').value = currentProvince;
            }
        });
    </script>
</x-app-layout>