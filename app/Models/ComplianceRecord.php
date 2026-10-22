<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * ComplianceRecord Model
 *
 * Represents a certification, license, or compliance document
 * belonging to a staff member, along with its expiry date.
 *
 * Example:
 *   Staff: Naomi Nosa
 *   Document: Professional Indemnity Insurance
 *   Expiry: 2026-12-31
 *   Status: Valid / Expiring Soon / Expired
 */
class ComplianceRecord extends Model
{
    use HasFactory;

    protected $table = 'compliance_records';

    protected $fillable = [
        // Who this record belongs to
        'staff_profile_id',

        // Document details
        'document_type',     // e.g. certification, license, insurance, contract, id_document
        'document_name',     // e.g. "ACCA Certificate", "Driver's License"
        'document_number',   // Reference/serial number on the document
        'issuing_body',      // Who issued it e.g. "ICAN", "FRSC"

        // Dates
        'issue_date',        // When it was issued
        'expiry_date',        // When it expires (nullable for documents that never expire)

        // File upload
        'document_file',     // Path to uploaded scanned copy

        // Status & notes
        'status',            // valid, expiring_soon, expired, renewal_in_progress
        'notes',
        'reminder_sent_at',  // When the last expiry reminder was sent
    ];

    protected $casts = [
        'issue_date'        => 'date',
        'expiry_date'       => 'date',
        'reminder_sent_at'  => 'datetime',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Each compliance record belongs to one staff member.
     */
    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES
    // =========================================================

    /**
     * Return a human-readable label for the document type.
     */
    public function getDocumentTypeLabelAttribute()
    {
        return match($this->document_type) {
            'certification'       => 'Certification',
            'license'             => 'License',
            'insurance'           => 'Insurance',
            'contract'            => 'Contract',
            'id_document'         => 'ID Document',
            'medical'             => 'Medical Certificate',
            'background_check'    => 'Background Check',
            default               => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }

    /**
     * Number of days until expiry. Negative if already expired.
     * Returns null if document has no expiry date.
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) return null;
        return now()->startOfDay()->diffInDays($this->expiry_date, false);
    }

    /**
     * Auto-determine the current status based on expiry date.
     * - No expiry date → 'valid' (permanent document)
     * - More than 30 days away → 'valid'
     * - 0-30 days away → 'expiring_soon'
     * - Past → 'expired'
     *
     * Note: 'renewal_in_progress' is set manually by HR and is NOT
     * overridden by this calculation.
     */
    public function getComputedStatusAttribute()
    {
        // If HR manually marked it as renewal in progress, respect that
        if ($this->status === 'renewal_in_progress') {
            return 'renewal_in_progress';
        }

        if (!$this->expiry_date) {
            return 'valid';
        }

        $days = $this->days_until_expiry;

        if ($days < 0)  return 'expired';
        if ($days <= 30) return 'expiring_soon';
        return 'valid';
    }

    /**
     * Return colour class for the status badge.
     */
    public function getStatusColorAttribute()
    {
        return match($this->computed_status) {
            'valid'               => 'badge-success',
            'expiring_soon'       => 'badge-warning',
            'expired'             => 'badge-danger',
            'renewal_in_progress' => 'badge-info',
            default               => 'badge-secondary',
        };
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->computed_status) {
            'valid'               => 'Valid',
            'expiring_soon'       => 'Expiring Soon',
            'expired'             => 'Expired',
            'renewal_in_progress' => 'Renewal In Progress',
            default               => ucfirst($this->computed_status),
        };
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Records expiring within the next 30 days (and not already expired).
     * Used for dashboard alerts.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Records that have already expired.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }

    /**
     * Filter by document type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}