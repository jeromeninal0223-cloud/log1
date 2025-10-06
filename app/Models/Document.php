<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'document_type',
        'description',
        'current_version',
        'created_by_id',
        'created_by_name',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all versions for this document
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the current version
     */
    public function currentVersion()
    {
        return $this->versions()->where('version_number', $this->current_version)->first();
    }

    /**
     * Get the latest version
     */
    public function latestVersion()
    {
        return $this->versions()->first();
    }

    /**
     * Get the creator of the document
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Create a new version of this document
     */
    public function createVersion(array $data): DocumentVersion
    {
        // Generate next version number
        $latestVersion = $this->versions()->first();
        $nextVersion = $this->generateNextVersion($latestVersion?->version_number ?? '0.0');

        $version = $this->versions()->create([
            'version_number' => $nextVersion,
            'modified_by_id' => $data['modified_by_id'],
            'modified_by_name' => $data['modified_by_name'],
            'user_role' => $data['user_role'],
            'changes_summary' => $data['changes_summary'] ?? null,
            'file_path' => $data['file_path'],
            'file_size' => $data['file_size'] ?? null,
            'status' => 'active',
            'metadata' => $data['metadata'] ?? null,
        ]);

        // Update current version
        $this->update(['current_version' => $nextVersion]);

        // Archive previous versions
        $this->versions()
            ->where('id', '!=', $version->id)
            ->update(['status' => 'archived']);

        return $version;
    }

    /**
     * Generate next version number
     */
    private function generateNextVersion(string $currentVersion): string
    {
        $parts = explode('.', $currentVersion);
        $major = (int)($parts[0] ?? 1);
        $minor = (int)($parts[1] ?? 0);

        // Increment minor version
        $minor++;

        return "{$major}.{$minor}";
    }

    /**
     * Scope for active documents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope by document type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
