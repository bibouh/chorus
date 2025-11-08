<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($member) {
            // Generate QR code image automatically when member is created
            if ($member->qr_code) {
                try {
                    $member->generateAndStoreQRCode();
                } catch (\Exception $e) {
                    // Log error but don't fail the creation
                    Log::error('Failed to generate QR code for member ' . $member->id . ': ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('qrcode')
            ->singleFile()
            ->acceptsMimeTypes(['image/png', 'image/svg+xml']);
    }

    /**
     * Generate and store QR code image in the qrcode collection.
     *
     * @param string|null $data The data to encode in the QR code (defaults to member's qr_code)
     * @param int $size Size of the QR code (default: 300)
     * @param string $format Format of the QR code (png or svg, default: png)
     * @return Media|null
     */
    public function generateAndStoreQRCode(?string $data = null, int $size = 300, string $format = 'png'): ?Media
    {
        // Use member's qr_code if no data provided
        $qrData = $data ?? $this->qr_code;

        if (!$qrData) {
            return null;
        }

        // Delete existing QR code if any
        $this->clearMediaCollection('qrcode');

        // Create temporary file path
        $tempPath = sys_get_temp_dir() . '/qrcode-' . $this->member_code . '-' . uniqid() . '.' . $format;

        // Generate QR code and save to temporary file
        QrCode::format($format)
            ->size($size)
            ->generate($qrData, $tempPath);

        // Store the QR code in the media collection
        $media = $this->addMedia($tempPath)
            ->usingName('QR Code - ' . $this->name)
            ->usingFileName('qrcode-' . $this->member_code . '.' . $format)
            ->toMediaCollection('qrcode');

        // Clean up temporary file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        return $media;
    }

    /**
     * Get the QR code media.
     *
     * @return Media|null
     */
    public function getQRCodeMedia(): ?Media
    {
        return $this->getFirstMedia('qrcode');
    }

    /**
     * Get the QR code URL.
     *
     * @return string|null
     */
    public function getQRCodeUrl(): ?string
    {
        $media = $this->getFirstMedia('qrcode');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the QR code image URL attribute (for JSON:API).
     *
     * @return string|null
     */
    public function getQrCodeImageUrlAttribute(): ?string
    {
        return $this->getQRCodeUrl();
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

    /**
     * Get the QR code image media (for JSON:API relationship).
     *
     * @return Media|null
     */
    public function qrCodeImage()
    {
        return $this->getFirstMedia('qrcode');
    }
}
