@extends('layouts.app')

@section('title', 'Editar Categoria - ' . $category->title)
@section('page-title', 'Editar Categoria')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorias</a></li>
    <li class="breadcrumb-item active">{{ $category->title }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit"></i> Editar Categoria</h3>
                </div>

                <form action="{{ route('categories.update', $category) }}" method="POST" class="needs-validation">
                    @csrf
                    @method('PATCH')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Título *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $category->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_slug">ID Slug *</label>
                            <input type="text" class="form-control @error('id_slug') is-invalid @enderror" id="id_slug"
                                name="id_slug" value="{{ old('id_slug', $category->id_slug) }}" required>
                            @error('id_slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="section">Seção *</label>
                                    <select class="form-control @error('section') is-invalid @enderror" id="section"
                                        name="section" required>
                                        <option value="">Selecione uma seção</option>
                                        @foreach ($sectionOptions as $val => $label)
                                            <option value="{{ $val }}" @selected(old('section', $category->section_value) == $val)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Removed 'Tipo de Pai' field from edit form per requirement --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="next">Próximo Nível</label>
                                    <select class="form-control @error('next') is-invalid @enderror" id="next"
                                        name="next">
                                        <option value="">Nenhum</option>
                                        @foreach ($sectionOptions as $val => $label)
                                            <option value="{{ $val }}" @selected(old('next', $category->next) == $val)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('next')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">Categoria Pai</label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                        name="parent_id">
                                        <option value="">Nenhuma</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>
                                                {{ $cat->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="icon">URL do Ícone</label>
                            <input type="url" class="form-control @error('icon') is-invalid @enderror" id="icon"
                                name="icon" value="{{ old('icon', $category->icon) }}">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand">Marca</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                        id="brand" name="brand" value="{{ old('brand', $category->brand) }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="help">Ajuda</label>
                                    <input type="text" class="form-control @error('help') is-invalid @enderror"
                                        id="help" name="help" value="{{ old('help', $category->help) }}">
                                    @error('help')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product">Produto (JSON)</label>
                            <textarea class="form-control @error('product') is-invalid @enderror" id="product" name="product" rows="4">{{ old('product', $category->product ? json_encode($category->product, JSON_PRETTY_PRINT) : '') }}</textarea>
                            <small class="form-text text-muted">JSON válido</small>
                            @error('product')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fas fa-save"></i> Atualizar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('categories.partials._slug-generator')
