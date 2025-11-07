<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRCodeDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'distribution_method',
        'sent_at',
        'sent_by',
        'include_instructions',
        'include_qr_image',
        'include_member_info',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'include_instructions' => 'boolean',
            'include_qr_image' => 'boolean',
            'include_member_info' => 'boolean',
        ];
    }

    /**
     * Get the member for this distribution.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who sent this distribution.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
