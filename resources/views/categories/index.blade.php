@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Gerenciador de Categorias')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Home</a></li>
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Suas Categorias</h3>
                    <div class="card-tools">
                        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle"></i> Nova Categoria
                        </a>
                    </div>
                </div>

                @if ($categories->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
                        <p class="mt-3 text-muted">Nenhuma categoria criada ainda.</p>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Criar Primeira Categoria
                        </a>
                    </div>
                @else
                    <div class="card-body p-0">
                        <div class="accordion" id="categoriesAccordion" data-level="root">
                            @foreach ($categories as $category)
                                <div class="accordion-item" data-category-id="{{ $category->id }}" draggable="true">
                                    @include('categories.partials._accordion-header', [
                                        'item' => $category,
                                        'collapseId' => 'collapse' . $category->id,
                                    ])
                                    @if ($category->children->count())
                                        <div id="collapse{{ $category->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $category->id }}" role="region">
                                            <div class="accordion-body p-0">
                                                <div class="list-group list-group-flush">
                                                    {{-- Body contains only children list; actions moved to header --}}

                                                    @if ($category->children->count())
                                                        <div class="accordion" id="subAccordion{{ $category->id }}"
                                                            data-level="1" data-parent-id="{{ $category->id }}">
                                                            @foreach ($category->children as $child)
                                                                <div class="accordion-item border-0 border-top indent-step-1"
                                                                    data-category-id="{{ $child->id }}"
                                                                    data-parent-id="{{ $category->id }}" draggable="true">
                                                                    @include(
                                                                        'categories.partials._accordion-header',
                                                                        [
                                                                            'item' => $child,
                                                                            'collapseId' =>
                                                                                'subCollapse' . $child->id,
                                                                        ]
                                                                    )
                                                                    @if ($child->children->count())
                                                                        <div id="subCollapse{{ $child->id }}"
                                                                            class="accordion-collapse collapse"
                                                                            aria-labelledby="heading{{ $child->id }}"
                                                                            role="region">
                                                                            <div class="accordion-body p-0">
                                                                                <div class="list-group list-group-flush">
                                                                                    {{-- Child actions are now in the header; body only shows grandchildren --}}

                                                                                    @if ($child->children->count())
                                                                                        <div class="accordion"
                                                                                            id="grandAccordion{{ $child->id }}"
                                                                                            data-level="2"
                                                                                            data-parent-id="{{ $child->id }}">
                                                                                            @foreach ($child->children as $grandchild)
                                                                                                <div class="accordion-item border-0 border-top indent-step-2"
                                                                                                    data-category-id="{{ $grandchild->id }}"
                                                                                                    data-parent-id="{{ $child->id }}"
                                                                                                    draggable="true">
                                                                                                    @include(
                                                                                                        'categories.partials._accordion-header',
                                                                                                        [
                                                                                                            'item' => $grandchild,
                                                                                                            'collapseId' =>
                                                                                                                'grandCollapse' .
                                                                                                                $grandchild->id,
                                                                                                        ]
                                                                                                    )
                                                                                                    @if ($grandchild->children->count())
                                                                                                        <div id="grandCollapse{{ $grandchild->id }}"
                                                                                                            class="accordion-collapse collapse"
                                                                                                            aria-labelledby="heading{{ $grandchild->id }}"
                                                                                                            role="region">
                                                                                                            <div
                                                                                                                class="accordion-body p-0">
                                                                                                                <div
                                                                                                                    class="list-group list-group-flush">
                                                                                                                    {{-- Grandchild actions are in the header; body contains no title or actions --}}
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="card card-success card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-code"></i> API Pública</h3>
                </div>
                <div class="card-body">
                    <p>O front-end pode consumir suas categorias em:</p>
                    <code class="d-block p-3 bg-light border rounded mb-3">GET /api/categories</code>
                    <p class="small text-muted mb-0">A resposta segue a estrutura especificada com: section, icon, title,
                        id, description, brand, help, next e product (o tipo de pai foi removido — use parent_id para
                        referência).</p>
                </div>
            </div>
        </div>
    </div>
@endsection

<link href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar drag-and-drop para cada nível do accordion
        // Incluir ROOT + todos os sub-accordions
        const accordions = document.querySelectorAll('[data-level], #categoriesAccordion');

        console.log('📦 Inicializando', accordions.length, 'accordions');

        // Rastrear informações do arrasto
        let dragStartContainer = null;
        let dragStartY = 0;
        let lastItemPassedId = null; // Último item que foi passado durante o arrasto
        let dragDirection = null; // 'up' ou 'down'

        accordions.forEach(accordion => {
            console.log('🔧 Configurando accordion:', accordion.id || accordion.dataset.parentId ||
                'root');

            new Sortable(accordion, {
                group: 'categories',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                handle: '.accordion-header',
                fallbackClass: 'sortable-fallback',
                onStart: function(evt) {
                    dragStartY = evt.originalEvent.clientY;
                    lastItemPassedId = null;
                    dragDirection = null;

                    const categoryId = evt.item.dataset.categoryId;
                    console.log('🔴 DRAG START - Item ID:', categoryId, 'Y:', dragStartY);
                },
                onMove: function(evt) {
                    // Detectar direção e qual item está sendo passado
                    const currentY = evt.originalEvent.clientY;

                    if (!dragDirection) {
                        dragDirection = currentY > dragStartY ? 'down' : 'up';
                        console.log('📍 Direção detectada:', dragDirection);
                    }

                    // Se há um item sendo sobreposto
                    if (evt.related && evt.related !== evt.item) {
                        const relatedId = evt.related.dataset.categoryId;
                        if (relatedId !== lastItemPassedId) {
                            lastItemPassedId = relatedId;
                            console.log('👉 Passou por item ID:', lastItemPassedId);
                        }
                    }
                },
                onEnd: function(evt) {
                    const item = evt.item;
                    const categoryId = item.dataset.categoryId;
                    const dragEndY = evt.originalEvent.clientY;

                    console.log('🔵 DRAG END - Item ID:', categoryId);
                    console.log('Direção final:', dragDirection, '(Y:', dragStartY, '→',
                        dragEndY, ')');
                    console.log('Último item passado:', lastItemPassedId);

                    let newParentId = null;
                    let shouldUpdate = false;

                    const oldParentId = parseInt(item.dataset.parentId || null) || null;

                    if (dragDirection === 'down') {
                        // PARA BAIXO: item passa por outros e vira filho do ÚLTIMO que passou
                        if (lastItemPassedId) {
                            newParentId = parseInt(lastItemPassedId);
                            console.log('↓ Desceu - filho do último item passado:',
                                newParentId);
                            shouldUpdate = (newParentId !== oldParentId);
                        } else {
                            console.log('↓ Desceu mas não passou por nenhum item');
                        }
                    } else if (dragDirection === 'up') {
                        // PARA CIMA: item vira irmão do ÚLTIMO que passou
                        // = vira filho do PAI do último item passado
                        if (lastItemPassedId) {
                            const lastItemPassed = document.querySelector(
                                `[data-category-id="${lastItemPassedId}"]`);
                            if (lastItemPassed) {
                                newParentId = parseInt(lastItemPassed.dataset.parentId ||
                                    null) || null;
                                console.log('↑ Subiu - irmão do item', lastItemPassedId,
                                    '- novo pai:', newParentId);
                                shouldUpdate = true; // Sempre atualiza quando sobe
                            }
                        }
                    }

                    console.log('Pai anterior:', oldParentId);
                    console.log('Novo pai:', newParentId);
                    console.log('---\n');

                    // Se mudou de pai, atualizar no servidor
                    if (shouldUpdate && newParentId !== oldParentId) {
                        updateCategoryParent(categoryId, newParentId, item);
                    } else {
                        console.log('❌ Movimento ignorado');
                    }
                }
            });
        });

        // Função para atualizar o pai da categoria no servidor (sem recarregar a página)
        function updateCategoryParent(categoryId, newParentId, categoryElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/categories/${categoryId}/reorder`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        parent_id: newParentId || null
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Erro ao atualizar categoria');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✓ Resposta do servidor:', data);

                    // Atualizar o DOM com as classes e atributos corretos
                    updateDOMAfterReorder(categoryElement, newParentId);

                    showAlert('success', data.message);
                })
                .catch(error => {
                    console.error('✗ Erro:', error);
                    showAlert('danger', error.message);
                    // Recarregar em caso de erro para reverter o movimento visual
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                });
        }

        // Função para atualizar o DOM após reorder bem-sucedido
        function updateDOMAfterReorder(categoryElement, newParentId) {
            // Atualizar data-parent-id
            if (newParentId) {
                categoryElement.dataset.parentId = newParentId;
            } else {
                categoryElement.removeAttribute('data-parent-id');
            }

            // Remover classes de indentação antigas
            categoryElement.classList.remove('indent-step-1', 'indent-step-2');
            categoryElement.classList.remove('border-0', 'border-top');

            // Determinar o nível de profundidade baseado no novo pai
            if (!newParentId) {
                // É um item raiz
                categoryElement.classList.remove('indent-step-1', 'indent-step-2', 'border-0', 'border-top');
            } else {
                // Calcular profundidade
                let parent = document.querySelector(`[data-category-id="${newParentId}"]`);
                let depth = 0;

                while (parent) {
                    const parentId = parent.dataset.parentId;
                    if (!parentId) break;
                    depth++;
                    parent = document.querySelector(`[data-category-id="${parentId}"]`);
                }

                // Adicionar classes baseado na profundidade
                categoryElement.classList.add('border-0', 'border-top');
                if (depth === 0) {
                    categoryElement.classList.add('indent-step-1');
                } else if (depth >= 1) {
                    categoryElement.classList.add('indent-step-2');
                }
            }

            // Atualizar o atributo draggable
            categoryElement.setAttribute('draggable', 'true');
        }

        // Função auxiliar para mostrar alertas
        function showAlert(type, message) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.setAttribute('role', 'alert');
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
            alert.innerHTML = `
                <i class="bi ${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            const container = document.querySelector('.app-content');
            container.insertBefore(alert, container.firstChild);

            // Auto-dismiss em 5 segundos
            setTimeout(() => {
                alert.classList.remove('show');
                alert.addEventListener('transitionend', () => alert.remove(), {
                    once: true
                });
            }, 5000);
        }
    });
</script>

<style>
    .sortable-ghost {
        opacity: 0.4;
        background: #e7f3ff;
    }

    .sortable-drag {
        opacity: 0.5;
    }

    .accordion-item {
        cursor: move;
        transition: background-color 0.2s;
    }

    .accordion-item:hover {
        background-color: #f9f9f9;
    }

    .accordion-item.sortable-ghost {
        background-color: #e7f3ff;
        border-left: 3px solid #0d6efd;
    }

    /* Indentação para mostrar hierarquia */
    .indent-step-1 {
        margin-left: 20px;
    }

    .indent-step-2 {
        margin-left: 40px;
    }
</style>
