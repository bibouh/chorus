<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Member extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'member_code',
        'qr_code',
        'name',
        'email',
        'phone',
        'address',
        'voice_part',
        'join_date',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the attendances for this member.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the QR code distributions for this member.
     */
    public function qrCodeDistributions()
    {
        return $this->hasMany(QRCodeDistribution::class);
    }

    /**
     * Get the QR code generation histories for this member.
     */
    public function qrCodeGenerationHistories()
    {
        return $this->hasMany(QRCodeGenerationHistory::class);
    }
}
