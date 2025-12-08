<?php

namespace App\Http\Controllers;

use App\Enums\CategorySection;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display dashboard with all categories
     */
    public function index(): View
    {
        $categories = Category::where('user_id', auth()->id())
            ->root()
            ->with('children.children')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show create category form
     */
    public function create(): View
    {
        $categories = Category::where('user_id', auth()->id())->get();

        return view('categories.create', compact('categories'))
            ->with('sectionOptions', CategorySection::options());
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'section' => ['required', Rule::in(\App\Enums\CategorySection::values())],
            'id_slug' => 'required|string|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|url',
            'brand' => 'nullable|string',
            'help' => 'nullable|string',
            'next' => ['nullable', Rule::in(\App\Enums\CategorySection::values())],
            'parent_id' => 'nullable|exists:categories,id',
            'product' => 'nullable|json',
        ]);

        $validated['user_id'] = auth()->id();
        $category = Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category): View
    {
        $this->authorize('view', $category);
        $category->load('children.children');

        return view('categories.show', compact('category'));
    }

    /**
     * Show edit category form
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);
        $categories = Category::where('user_id', auth()->id())
            ->where('id', '!=', $category->id)
            ->get();

        return view('categories.edit', compact('category', 'categories'))
            ->with('sectionOptions', CategorySection::options());
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'section' => ['sometimes', Rule::in(\App\Enums\CategorySection::values())],
            'id_slug' => 'sometimes|string|unique:categories,id_slug,'.$category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|url',
            'brand' => 'nullable|string',
            'help' => 'nullable|string',
            'next' => ['nullable', Rule::in(\App\Enums\CategorySection::values())],
            'parent_id' => 'nullable|exists:categories,id',
            'product' => 'nullable|json',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json(['message' => 'Categoria deletada com sucesso'], 200);
    }

    /**
     * Get categories as JSON with hierarchy (for front-end consumption)
     */
    public function getJson(): JsonResponse
    {
        $categories = Category::where('user_id', auth()->id())->get();

        // toJsonFormat() agora já inclui o parent (id_slug do pai)
        $formatted = $categories->map(fn ($cat) => $cat->toJsonFormat())->toArray();

        return response()->json($formatted);
    }

    /**
     * Reorder/move a category (change parent_id and potentially section)
     */
    public function reorder(Request $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $newParentId = $validated['parent_id'] ?? null;
        $oldParentId = $category->parent_id;

        // Validar para prevenir loops (uma categoria não pode ser pai de si mesma)
        if ($newParentId && $newParentId === $category->id) {
            return response()->json([
                'message' => 'Uma categoria não pode ser pai de si mesma',
            ], 422);
        }

        // Validar para prevenir loops indiretos
        if ($newParentId && $this->isCategoryDescendant($category, $newParentId)) {
            return response()->json([
                'message' => 'Não é possível mover uma categoria para dentro de seus filhos',
            ], 422);
        }

        // Atualizar seção baseado no novo pai
        if ($newParentId) {
            $parentCategory = Category::find($newParentId);
            // Se o pai tem um "próximo" tipo definido, a categoria herda esse tipo
            $category->section = $parentCategory?->next ?? CategorySection::TIPO;
        } else {
            // Se não tem pai, é uma categoria raiz = TIPO
            $category->section = CategorySection::TIPO;
        }

        // Atualizar parent_id e section
        $category->update([
            'parent_id' => $newParentId,
            'section' => $category->section,
        ]);

        return response()->json([
            'message' => 'Categoria reorganizada com sucesso',
            'category' => $category->toJsonFormat(),
            'parent_id' => $newParentId,
        ]);
    }

    /**
     * Verificar se uma categoria é descendente de outra
     */
    private function isCategoryDescendant(Category $category, ?int $potentialChildId): bool
    {
        if (! $potentialChildId) {
            return false;
        }

        $child = Category::find($potentialChildId);
        if (! $child) {
            return false;
        }

        // Se a categoria filho é descendente da categoria atual
        while ($child->parent_id) {
            $child = $child->parentCategory;
            if ($child->id === $category->id) {
                return true;
            }
        }

        return false;
    }
}
