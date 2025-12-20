<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'title',
        'section',
        'id_slug',
        'description',
        'icon',
        'brand',
        'help',
        'next',
        'parent_id',
        'product',
    ];

    protected static function booted(): void
    {
        // Auto-generate id_slug quando criado ou título é modificado
        static::creating(function ($model) {
            if (! $model->id_slug) {
                $model->id_slug = $model->generateSlug();
            }
        });

        // Atualizar slug quando title ou parent_id muda
        static::saving(function ($model) {
            if ($model->isDirty(['title', 'parent_id'])) {
                $model->id_slug = $model->generateSlug();
            }
        });
    }

    /**
     * Get the route key for the model (uses id_slug for API routes)
     */
    public function getRouteKeyName(): string
    {
        return 'id_slug';
    }

    protected $casts = [
        'product' => 'json',
        // cast enum fields
        'section' => \App\Enums\CategorySection::class,
        'next' => \App\Enums\CategorySection::class,
    ];

    // Using CategorySection enum for section values (DRY/KISS)

    // Relação: Pertence a um usuário (loja)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relação: Uma categoria pode ter muitas subcategorias
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relação: Uma categoria pertence a uma categoria pai
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Escopo para categorias raiz
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // Formata a categoria para JSON conforme especificado
    public function toJsonFormat(): array
    {
        return [
            'section' => $this->section_value,
            'section_label' => $this->section_label,
            'icon' => $this->icon,
            'title' => $this->title,
            'id' => $this->id_slug,
            'description' => $this->description,
            'brand' => $this->brand,
            'help' => $this->help,
            'next' => $this->next_value,
            'parent' => $this->parentCategory?->id_slug,
            'product' => $this->product ?? ['id' => null],
        ];
    }

    /**
     * Get the human-readable label for the section
     */
    public function getSectionLabelAttribute(): string
    {
        return $this->section instanceof \App\Enums\CategorySection
            ? $this->section->label()
            : (\App\Enums\CategorySection::tryFrom($this->section)?->label() ?? (string) $this->section);
    }

    /**
     * Get section value as string (handles enum or string)
     */
    public function getSectionValueAttribute(): string
    {
        if ($this->section instanceof \App\Enums\CategorySection) {
            return $this->section->value;
        }

        return (string) $this->section;
    }

    /**
     * Get next value as string (handles enum or string)
     */
    public function getNextValueAttribute(): ?string
    {
        if ($this->next instanceof \App\Enums\CategorySection) {
            return $this->next->value;
        }

        return $this->next ? (string) $this->next : null;
    }

    /**
     * Get next label as string (handles enum or string)
     */
    public function getNextLabelAttribute(): ?string
    {
        if ($this->next instanceof \App\Enums\CategorySection) {
            return $this->next->label();
        }

        return $this->next ? (\App\Enums\CategorySection::options()[$this->next] ?? $this->next) : null;
    }

    /**
     * Gera slug automático no formato: "nome-parent_slug" ou apenas "nome" se for root
     * Também remove "-parent_slug" antigo se estiver mudando de parent
     */
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->title);

        // Se tem parent, append o id_slug do parent
        if ($this->parent_id) {
            $parent = Category::find($this->parent_id);
            if ($parent) {
                $baseSlug = "{$baseSlug}-{$parent->id_slug}";
            }
        }

        // Garantir unicidade na store
        return $this->ensureUniqueSlug($baseSlug);
    }

    /**
     * Garante que o slug seja único dentro da store
     */
    private function ensureUniqueSlug(string $slug): string
    {
        $query = Category::where('store_id', $this->store_id)
            ->where('id_slug', $slug);

        // Se é update, exclui o registro atual
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        if ($query->exists()) {
            // Adicionar número para tornar único
            $counter = 1;
            $newSlug = "{$slug}-{$counter}";

            while (Category::where('store_id', $this->store_id)
                ->where('id_slug', $newSlug)
                ->where('id', '!=', $this->id ?? null)
                ->exists()) {
                $counter++;
                $newSlug = "{$slug}-{$counter}";
            }

            return $newSlug;
        }

        return $slug;
    }
}
