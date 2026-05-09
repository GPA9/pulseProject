<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div style="height:4px;background:linear-gradient(90deg,#10b981,#059669);border-radius:16px 16px 0 0;"></div>
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="fw-bold"><i class="bi bi-calendar-event me-2" style="color:#10b981;"></i>Editar Concierto</h3>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </a>
                        </div>

                        <form method="POST" action="{{ route('concerts.update', $concert->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Venue</label>
                                    <input type="text" name="venue" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('venue', $concert->venue) }}" required>
                                    @error('venue')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Ciudad</label>
                                    <input type="text" name="city" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('city', $concert->city) }}" required>
                                    @error('city')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Dirección</label>
                                    <input type="text" name="address" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('address', $concert->address) }}" required>
                                    @error('address')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Provincia</label>
                                    <input type="text" name="province" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('province', $concert->province) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Comunidad Autónoma</label>
                                    <select name="autonomous_community" class="form-select bg-black border-secondary text-light" required>
                                        @php $communities = \App\Http\Controllers\ConcertController::getCommunitiesMap(); @endphp
                                        @foreach($communities as $comm => $provs)
                                            <option value="{{ $comm }}" {{ old('autonomous_community', $concert->autonomous_community) === $comm ? 'selected' : '' }}>{{ $comm }}</option>
                                        @endforeach
                                    </select>
                                    @error('autonomous_community')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Fecha y hora</label>
                                    <input type="datetime-local" name="date" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('date', $concert->date->format('Y-m-d\TH:i')) }}" required>
                                    @error('date')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Precio (€)</label>
                                    <input type="number" name="price" class="form-control bg-black border-secondary text-light" 
                                           step="0.01" min="0" value="{{ old('price', $concert->price) }}" required>
                                    @error('price')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Aforo</label>
                                    <input type="number" name="capacity" class="form-control bg-black border-secondary text-light" 
                                           min="1" value="{{ old('capacity', $concert->capacity) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Género</label>
                                    <input type="text" name="genre" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('genre', $concert->genre) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Enlace Ticketmaster</label>
                                    <input type="url" name="ticketmaster_url" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('ticketmaster_url', $concert->ticketmaster_url) }}" required 
                                           placeholder="https://www.ticketmaster.es/event/...">
                                    <small class="text-muted">Pega aquí el enlace directo al evento en Ticketmaster</small>
                                    @error('ticketmaster_url')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Descripción</label>
                                    <textarea name="description" class="form-control bg-black border-secondary text-light" rows="3">{{ old('description', $concert->description) }}</textarea>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
