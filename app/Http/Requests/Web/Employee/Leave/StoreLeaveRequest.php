<?php

namespace App\Http\Requests\Web\Employee\Leave;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\LeaveApplication;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);

            // Durasi maksimal 12 hari
            $maxDays = 12;
            if ($startDate->diffInDays($endDate) + 1 > $maxDays) {
                $validator->errors()->add('end_date', "Leave duration may not exceed {$maxDays} days.");
            }

            // Tidak boleh bentrok dengan cuti sebelumnya
            $overlappingLeave = LeaveApplication::where('user_id', Auth::user()->id)
                ->where('status', '!=', 'rejected')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($query) use ($startDate, $endDate) {
                              $query->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                          });
                })
                ->exists();

            if ($overlappingLeave) {
                $validator->errors()->add('start_date', 'You already have leave in this date range.');
            }
        });
    }
}