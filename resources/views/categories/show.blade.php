@extends('layouts.app')

@section('title', $category->title)
@section('page-title', $category->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorias</a></li>
    <li class="breadcrumb-item active">{{ $category->title }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ $category->title }}</h3>
                <div class="card-tools">
                    @can('update', $category)
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    @endcan
                    @can('delete', $category)
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
                                <i class="bi bi-trash"></i> Deletar
                            </button>
                        </form>
                    @endcan
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">ID Slug</h6>
                        <code>{{ $category->id_slug }}</code>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Section</h6>
                        <span class="badge bg-info">{{ $category->section_label }}</span>
                    </div>
                </div>

                @if($category->description)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Descrição</h6>
                            <p class="mb-0">{{ $category->description }}</p>
                        </div>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Próximo Nível</h6>
                        @if($category->next)
                            <span class="badge bg-success">{{ $category->next_label }}</span>
                        @else
                            <span class="badge bg-danger">Final</span>
                        @endif
                    </div>
                    {{-- Parent Level removed from view (not necessary to display the parent type here) --}}
                </div>

                @if($category->brand || $category->help || $category->icon)
                    <hr>
                @endif

                @if($category->brand)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Brand</h6>
                            <p class="mb-0">{{ $category->brand }}</p>
                        </div>
                    </div>
                @endif

                @if($category->help)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Help</h6>
                            <p class="mb-0">{{ $category->help }}</p>
                        </div>
                    </div>
                @endif

                @if($category->icon)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Icon URL</h6>
                            <p class="mb-0"><a href="{{ $category->icon }}" target="_blank" class="link-primary">{{ $category->icon }}</a></p>
                        </div>
                    </div>
                @endif

                @if($category->product)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Product Data</h6>
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($category->product, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-2">JSON Output</h6>
                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($category->toJsonFormat(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        @if($category->children->count())
            <div class="card card-info card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-diagram-3"></i> Subcategorias ({{ $category->children->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <strong>{{ $child->title }}</strong>
                                    <span class="badge bg-secondary">{{ $child->section_label }}</span>
                                </div>
                                @if($child->description)
                                    <small class="text-muted">{{ $child->description }}</small>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
