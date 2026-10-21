<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class Noticeboard extends Model
{
    use HasFactory;

    protected $table = 'noticeboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_title', 'notice', 'start_date', 'start_time', 'end_date', 'end_time', 'status', 'show_on_website', 'image', 'school_id','session_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'show_on_website' => 'boolean',
    ];

    public function isVisibleOnWebsite(CarbonInterface $today): bool
    {
        if (! $this->status || ! $this->show_on_website) {
            return false;
        }

        if (blank($this->end_date)) {
            return true;
        }

        try {
            $endDate = CarbonImmutable::createFromFormat('m/d/Y', $this->end_date)->endOfDay();
        } catch (Throwable) {
            return true;
        }

        return $endDate->greaterThanOrEqualTo($today);
    }
}
