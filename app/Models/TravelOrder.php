<?php

namespace App\Models;

use App\Enum\TravelOrderStatus;
use App\States\ApprovedState;
use App\States\CancelledState;
use App\States\RequestedState;
use App\States\TravelOrderState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    use HasUlids, HasFactory;

    protected $fillable = [
            'user_id',
            'applicant_name',
            'destination',
            'departure_date',
            'return_date',
        ];
    protected $guarded = ['status'];
    
    protected $casts = [
        'status' => TravelOrderStatus::class,
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(static function ($model) {
            $model->status = TravelOrderStatus::REQUESTED;
        });
        static::addGlobalScope(static fn ($query) => $query->where('user_id', auth()->user()->id));
    }

    public function getState(): TravelOrderState
    {
        return match ($this->status) {
            TravelOrderStatus::REQUESTED => new RequestedState(),
            TravelOrderStatus::CANCELLED => new CancelledState(),
            TravelOrderStatus::APPROVED => new ApprovedState(),
            default => throw new \DomainException('Invalid Status'),
        };
    }

    public function setState(TravelOrderStatus $newStatus): void
    {
        $currentState = $this->getState();
        $currentState->transitionTo($this, $newStatus);
    }
    
}
