<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'type',
        'status',
        'segment_filters',
        'recipient_count',
        'scheduled_at',
        'sent_at',
        'sent_count',
        'opened_count',
        'clicked_count',
        'converted_count',
        'revenue_generated',
    ];

    protected $casts = [
        'segment_filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'revenue_generated' => 'decimal:2',
    ];

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function getOpenRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function getConversionRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->converted_count / $this->sent_count) * 100, 2);
    }
}
