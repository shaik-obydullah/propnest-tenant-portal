<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'property_id',
        'tenancy_id',
        'uploaded_by',
        'title',
        'category',
        'file_path',
        'version',
        'visibility',
        'file_size',
        'mime_type',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return config('portal.document_categories.'.$this->category, ucfirst($this->category));
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = max($this->file_size, 1024);

        return match (true) {
            $bytes >= 1_048_576 => number_format($bytes / 1_048_576, 1).' MB',
            $bytes >= 1024 => number_format($bytes / 1024, 0).' KB',
            default => $bytes.' B',
        };
    }
}
