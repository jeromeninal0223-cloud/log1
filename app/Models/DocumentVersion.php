<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_number',
        'modified_by_id',
        'modified_by_name',
        'user_role',
        'changes_summary',
        'file_path',
        'file_size',
        'status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the document this version belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who modified this version
     */
    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    /**
     * Check if this is the current version
     */
    public function isCurrent(): bool
    {
        return $this->document && $this->document->current_version === $this->version_number;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        
        $bytes = $this->file_size;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log(1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-success',
            'archived' => 'bg-secondary',
            'deleted' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Restore this version (create new version based on this one)
     */
    public function restore(array $restoreData): DocumentVersion
    {
        return $this->document->createVersion([
            'modified_by_id' => $restoreData['modified_by_id'],
            'modified_by_name' => $restoreData['modified_by_name'],
            'user_role' => $restoreData['user_role'],
            'changes_summary' => "Restored from version {$this->version_number}",
            'file_path' => $this->file_path, // Use same file path
            'file_size' => $this->file_size,
            'metadata' => $this->metadata,
        ]);
    }

    /**
     * Scope for active versions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for archived versions
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope by document
     */
    public function scopeForDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId);
    }
}
