<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description', 
        'schedule',
        'duration',
        'course_id',
        'room',
        'lesson_type',
        'qr_code'
    ];

    protected $casts = [
        'schedule' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFormattedScheduleAttribute()
    {
        return $this->schedule->format('M d, Y h:i A');
    }

    public function getStatusAttribute()
    {
        return $this->calculateStatus();
    }
    
    /**
     * Calculate lecture status
     */
    public function calculateStatus()
    {
        $now = now();
        $schedule = Carbon::parse($this->schedule);
        $endTime = $schedule->copy()->addMinutes($this->duration);
        
        if ($now->lt($schedule)) {
            return 'upcoming';
        } elseif ($now->between($schedule, $endTime)) {
            return 'ongoing';
        } else {
            return 'completed';
        }
    }
    
    /**
     * Check if QR code is valid (within 5 minutes before to 5 minutes after start)
     */
    public function isQRCodeValid()
    {
        $now = now();
        $lectureTime = Carbon::parse($this->schedule);
        
        // Allow QR code from 5 minutes before to 5 minutes after lecture starts
        $validStartTime = $lectureTime->copy()->subMinutes(5);
        $validEndTime = $lectureTime->copy()->addMinutes(5);
        
        return $now->between($validStartTime, $validEndTime);
    }
    
    /**
     * Get QR code validity window
     */
    public function getQRValidityWindow()
    {
        $lectureTime = Carbon::parse($this->schedule);
        $validFrom = $lectureTime->copy()->subMinutes(5)->format('h:i A');
        $validTo = $lectureTime->copy()->addMinutes(5)->format('h:i A');
        
        return "Valid from {$validFrom} to {$validTo}";
    }
    
    /**
     * Get time until QR expires (in minutes)
     */
    public function getMinutesUntilQRExpires()
    {
        $now = now();
        $lectureTime = Carbon::parse($this->schedule);
        $validEndTime = $lectureTime->copy()->addMinutes(5);
        
        if ($now->gt($validEndTime)) {
            return 0; // Already expired
        }
        
        return max(0, $now->diffInMinutes($validEndTime, false));
    }
    
    /**
     * Get time until QR becomes valid (in minutes)
     */
    public function getMinutesUntilQRValid()
    {
        $now = now();
        $lectureTime = Carbon::parse($this->schedule);
        $validStartTime = $lectureTime->copy()->subMinutes(5);
        
        if ($now->gte($validStartTime)) {
            return 0; // Already valid or past
        }
        
        return max(0, $validStartTime->diffInMinutes($now, false));
    }
    
    /**
     * Get time until lecture starts (in minutes)
     */
    public function getMinutesUntilStart()
    {
        $now = now();
        $lectureTime = Carbon::parse($this->schedule);
        
        $minutes = $now->diffInMinutes($lectureTime, false);
        return $minutes > 0 ? $minutes : 0;
    }
    
    /**
     * Get real-time status data with timezone info
     */
    public function getRealtimeStatusData()
    {
        $now = now();
        $lectureTime = Carbon::parse($this->schedule);
        $validStartTime = $lectureTime->copy()->subMinutes(5);
        $validEndTime = $lectureTime->copy()->addMinutes(5);
        
        $qrValid = $now->between($validStartTime, $validEndTime);
        $minutesUntilStart = max(0, $now->diffInMinutes($lectureTime, false));
        $minutesUntilQRValid = $now->lt($validStartTime) ? max(0, $validStartTime->diffInMinutes($now, false)) : 0;
        $minutesUntilQRExpires = $now->lt($validEndTime) ? max(0, $now->diffInMinutes($validEndTime, false)) : 0;
        
        return [
            'status' => $this->calculateStatus(),
            'qr_valid' => $qrValid,
            'qr_validity_window' => $this->getQRValidityWindow(),
            'minutes_until_qr_expires' => $minutesUntilQRExpires,
            'minutes_until_qr_valid' => $minutesUntilQRValid,
            'minutes_until_start' => $minutesUntilStart,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'current_time_formatted' => $now->format('h:i:s A'),
            'lecture_time' => $lectureTime->format('Y-m-d H:i:s'),
            'lecture_time_formatted' => $lectureTime->format('h:i A'),
            'qr_valid_from' => $validStartTime->format('h:i A'),
            'qr_valid_to' => $validEndTime->format('h:i A'),
            'timezone' => config('app.timezone'),
            'timezone_offset' => $now->getOffset() / 3600,
        ];
    }
}