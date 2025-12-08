@extends('layouts.app')

@section('title', 'Nova Categoria')
@section('page-title', 'Nova Categoria')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorias</a></li>
    <li class="breadcrumb-item active">Criar</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus"></i> Nova Categoria</h3>
                </div>

                <form action="{{ route('categories.store') }}" method="POST" class="needs-validation">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Título *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_slug">ID Slug *</label>
                            <input type="text" class="form-control @error('id_slug') is-invalid @enderror" id="id_slug"
                                name="id_slug" value="{{ old('id_slug') }}" required placeholder="ex: normal-grau">
                            <small class="form-text text-muted">Identificador único (sem espaços)</small>
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
                                            <option value="{{ $val }}" @selected(old('section') == $val)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Removed 'Tipo de Pai' field as per UX requirement (parent type handled implicitly by parent_id) --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="next">Próximo Nível</label>
                                    <select class="form-control @error('next') is-invalid @enderror" id="next"
                                        name="next">
                                        <option value="">Nenhum</option>
                                        @foreach ($sectionOptions as $val => $label)
                                            <option value="{{ $val }}" @selected(old('next') == $val)>
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
                                            <option value="{{ $cat->id }}" @selected(old('parent_id') == $cat->id)>
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
                                rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="icon">URL do Ícone</label>
                            <input type="url" class="form-control @error('icon') is-invalid @enderror" id="icon"
                                name="icon" value="{{ old('icon') }}" placeholder="https://example.com/icon.png">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand">Marca</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                        id="brand" name="brand" value="{{ old('brand') }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="help">Ajuda</label>
                                    <input type="text" class="form-control @error('help') is-invalid @enderror"
                                        id="help" name="help" value="{{ old('help') }}">
                                    @error('help')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product">Produto (JSON)</label>
                            <textarea class="form-control @error('product') is-invalid @enderror" id="product" name="product" rows="4"
                                placeholder='{"id": null}'>{{ old('product') }}</textarea>
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
                            <i class="fas fa-save"></i> Criar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Estrutura</h3>
                </div>
                <div class="card-body">
                    <p><strong>Hierarquia de Categorias:</strong></p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-folder text-primary"></i> <strong>Tipo de Lente</strong> (Nível 1)</li>
                        <li style="margin-left: 20px;">
                            <i class="fas fa-folder text-warning"></i> <strong>Espessura</strong> (Nível 2)
                        </li>
                        <li style="margin-left: 40px;">
                            <i class="fas fa-folder text-success"></i> <strong>Produto</strong> (Nível 3)
                        </li>
                    </ul>

                    <hr>

                    <p><strong>Exemplo de JSON:</strong></p>
                    <pre style="font-size: 11px; background: #f5f5f5; padding: 10px; border-radius: 4px;">
{
  "section": "espessura",
  "icon": "http://...",
  "title": "Normal",
  "id": "normal-grau",
  "next": "produto",
  "product": {
    "id": null
  }
}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('categories.partials._slug-generator')
