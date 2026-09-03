<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'is_default',
        'is_rental',
    ];

    protected $casts = [
        'type'       => CategoryType::class,
        'is_default' => 'boolean',
        'is_rental'  => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** Scope: categories belonging to this specific user only */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeRental(Builder $query): Builder
    {
        return $query->where('is_rental', true);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->where('is_rental', false);
    }

    public static function defaultRentalCategories(): array
    {
        return [
            // Pemasukan Rental
            ['name' => 'Sewa Lepas Kunci', 'type' => 'income', 'icon' => '🚗', 'color' => '#10B981', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Sewa + Supir', 'type' => 'income', 'icon' => '👨‍✈️', 'color' => '#059669', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Antar-Jemput / Drop Off', 'type' => 'income', 'icon' => '🛫', 'color' => '#0D9488', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Denda / Biaya Overtime', 'type' => 'income', 'icon' => '⏱️', 'color' => '#D97706', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Klaim Kerusakan / Ganti Rugi', 'type' => 'income', 'icon' => '🛡️', 'color' => '#2563EB', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Pendapatan Lainnya', 'type' => 'income', 'icon' => '📦', 'color' => '#64748B', 'is_rental' => true, 'is_default' => true],

            // Pengeluaran Rental
            ['name' => 'Bahan Bakar (BBM / Bensin)', 'type' => 'expense', 'icon' => '⛽', 'color' => '#DC2626', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Servis Rutin & Ganti Oli', 'type' => 'expense', 'icon' => '🔧', 'color' => '#EA580C', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Cuci & Salon Mobil', 'type' => 'expense', 'icon' => '🧼', 'color' => '#0284C7', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Gaji / Uang Makan Supir', 'type' => 'expense', 'icon' => '💵', 'color' => '#4F46E5', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Tol & Parkir Operasional', 'type' => 'expense', 'icon' => '🛣️', 'color' => '#78716C', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Perpanjangan Pajak / STNK', 'type' => 'expense', 'icon' => '📄', 'color' => '#0891B2', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Perbaikan & Sparepart Ban', 'type' => 'expense', 'icon' => '⚙️', 'color' => '#D97706', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Asuransi Kendaraan', 'type' => 'expense', 'icon' => '🛡️', 'color' => '#2563EB', 'is_rental' => true, 'is_default' => true],
            ['name' => 'Biaya Operasional Lainnya', 'type' => 'expense', 'icon' => '📦', 'color' => '#64748B', 'is_rental' => true, 'is_default' => true],
        ];
    }
}
