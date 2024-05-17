<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const LEAD_TYPES = [
       'ENQUIRY' => 'Enquiry',
       'PURCHASE' => 'Purchase',
       'MESSAGE' => 'Message',
       'PHONE_CALL' => 'Phone call',
       'FOLLOW_UP' => 'Follow up',
       'OTHER' => 'other',
    ];

    public const LEAD_STATUS = [
       0 => 'NEW',
       1 => 'ACCEPTED',
       2 => 'IGNORED',
       3 => 'IN PROGRESS',
       4 => 'DROPPED',
       5 => 'DONE',
    ];

    protected $fillable = [
        'lead_name',
        'from',
        'lead_type',
        'lead_status',
        'lead_description',
        'accepted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLeadTypeStringAttribute(): string
    {
        foreach (self::LEAD_TYPES as $key => $type) {
            if ($type === $this->lead_type) {
                return $key;
            }
        }
        return 'OTHER';
    }

    public function getLeadStatusStringAttribute(): string
    {
        return self::LEAD_STATUS[$this->lead_status];
    }
}
