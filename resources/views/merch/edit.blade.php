<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#d97706);border-radius:16px 16px 0 0;"></div>
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="fw-bold"><i class="bi bi-bag me-2" style="color:#f59e0b;"></i>Editar Producto</h3>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </a>
                        </div>

                        <form method="POST" action="{{ route('merch.update', $merch->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted">Nombre del producto</label>
                                    <input type="text" name="name" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('name', $merch->name) }}" required>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Categoría</label>
                                    <select name="category" class="form-select bg-black border-secondary text-light" required>
                                        <option value="">Selecciona categoría</option>
                                        @foreach(['Camisetas','Sudaderas','Gorras','Accesorios','Otros'] as $cat)
                                            <option value="{{ $cat }}" {{ old('category', $merch->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Precio (€)</label>
                                    <input type="number" name="price" class="form-control bg-black border-secondary text-light" 
                                           step="0.01" min="0" value="{{ old('price', $merch->price) }}" required>
                                    @error('price')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Ciudad</label>
                                    <input type="text" name="city" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('city', $merch->city) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tallas (separadas por coma)</label>
                                    <input type="text" name="sizes" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('sizes', is_array($merch->sizes) ? implode(', ', $merch->sizes) : '') }}" 
                                           placeholder="S, M, L, XL">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Enlace Merchbar</label>
                                    <input type="url" name="merchbar_url" class="form-control bg-black border-secondary text-light" 
                                           value="{{ old('merchbar_url', $merch->merchbar_url) }}" required 
                                           placeholder="https://www.merchbar.com/...">
                                    <small class="text-muted">Pega aquí el enlace directo al producto en Merchbar</small>
                                    @error('merchbar_url')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Descripción</label>
                                    <textarea name="description" class="form-control bg-black border-secondary text-light" rows="3">{{ old('description', $merch->description) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Imagen del producto</label>
                                    @if($merch->image_path)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $merch->image_path) }}" class="rounded" style="width:100px;height:100px;object-fit:cover;" alt="">
                                            <small class="text-muted ms-2">Imagen actual</small>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control bg-black border-secondary text-light" accept="image/*">
                                    <small class="text-muted">Deja vacío para mantener la imagen actual</small>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                                    <button type="submit" class="btn btn-warning">
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
