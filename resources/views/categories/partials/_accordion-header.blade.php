@props(['item', 'collapseId' => null])

@php
    $hasChildren = $item->children && $item->children->count();
    $headingId = 'heading' . $item->id;
    $actionsId = 'actions' . class_basename($item) . $item->id; // unique id
    $collapseId = $collapseId ?? 'collapse' . $item->id;
@endphp

<h2 class="accordion-header d-flex align-items-center" id="{{ $headingId }}">
    @if($hasChildren)
        <button id="{{ $headingId }}" class="accordion-button collapsed flex-grow-1 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
            <i class="bi bi-chevron-right accordion-toggle-icon me-2"></i>
            <strong>{{ $item->title }}</strong>
            <span class="badge bg-info ms-2">{{ $item->section_label }}</span>
            @if($item->children->count())
                <span class="badge bg-secondary ms-2">{{ $item->children->count() }} itens</span>
            @endif
        </button>
    @else
        <button id="{{ $headingId }}" class="accordion-button no-children flex-grow-1 text-start" type="button" aria-expanded="false">
            <i class="bi bi-chevron-right accordion-toggle-icon me-2"></i>
            <strong>{{ $item->title }}</strong>
            <span class="badge bg-info ms-2">{{ $item->section_label }}</span>
        </button>
    @endif

    <div class="dropdown ms-2">
        <button class="btn btn-sm btn-light dropdown-toggle py-0 px-2" type="button" id="{{ $actionsId }}" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="Ações para {{ $item->title }}">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="{{ $actionsId }}">
            @can('view', $item)
                <li><a class="dropdown-item" href="{{ route('categories.show', $item) }}"><i class="bi bi-eye me-2"></i>Ver</a></li>
            @endcan
            @can('update', $item)
                <li><a class="dropdown-item" href="{{ route('categories.edit', $item) }}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
            @endcan
            @can('delete', $item)
                <li>
                    <form action="{{ route('categories.destroy', $item) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Deletar</button>
                    </form>
                </li>
            @endcan
        </ul>
    </div>
</h2>
