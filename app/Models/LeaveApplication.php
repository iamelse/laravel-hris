<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Yogameleniawan\SearchSortEloquent\Traits\Searchable;
use Yogameleniawan\SearchSortEloquent\Traits\Sortable;

class LeaveApplication extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $guarded = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the formatted leave period (e.g. "3 days Jun 10–12 2025").
     */
    protected function formattedLeavePeriod(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->start_date || !$this->end_date) {
                    return '[invalid date]';
                }

                $start = Carbon::parse($this->start_date);
                $end = Carbon::parse($this->end_date);
                $days = $start->diffInDays($end) + 1; // include both start and end

                // Example: Jun 10–12 2025
                $formattedDate = $start->format('M j');
                if ($start->format('M Y') === $end->format('M Y')) {
                    // same month and year
                    $formattedDate .= ' – ' . $end->format('j Y');
                } elseif ($start->year === $end->year) {
                    // same year
                    $formattedDate .= ' – ' . $end->format('M j Y');
                } else {
                    // different years
                    $formattedDate .= ' ' . $start->year . ' – ' . $end->format('M j Y');
                }

                return "{$days} days, {$formattedDate}";
            }
        );
    }

    /**
     * Get the formmated user's created_at.
     * @return Attribute
     */
    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at
                ? Carbon::parse($this->created_at)->format('d M, Y H:i')
                : '[null]'
        );
    }

    /**
     * Get the formmated user's updated_at.
     * @return Attribute
     */
    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at
                ? Carbon::parse($this->updated_at)->format('d M, Y H:i')
                : '[null]'
        );
    }
}
