<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'user_id',
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
}
