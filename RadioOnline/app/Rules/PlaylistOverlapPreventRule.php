<?php

namespace App\Rules;

use app\Helpers\ApiResponse;
use App\Models\Playlist;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlaylistOverlapPreventRule implements ValidationRule
{

    protected $channelPlaylist;
    protected $startDate;
    protected $endDate;
    protected $startTime;
    protected $endTime;
    protected $ignoreId; // For self updates

    /**
     * @param $channelPlaylist
     * @param $startDate
     * @param $endDate
     * @param $startTime
     * @param $endTime
     * @param $ignoreId
     */
    public function __construct($channelPlaylist, $startDate, $endDate, $startTime, $endTime, $ignoreId = null)
    {
        $this->channelPlaylist = $channelPlaylist;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlap = Playlist::query()
            ->where('channel_playlist', $this->channelPlaylist)
            ->when($this->ignoreId, function ($q) {
                $q->where('id', '!=', $this->ignoreId);
            })
            ->where(function ($query) {
                $query
                    ->whereBetween('start_date', [$this->startDate, $this->endDate])
                    ->orWhereBetween('end_date', [$this->startDate, $this->endDate]);
            })
            ->where(function ($query) {
                $query
                    ->whereBetween('start_time', [$this->startTime, $this->endTime])
                    ->orWhereBetween('end_time', [$this->startTime, $this->endTime]);
            })
            ->first();
        if ($overlap)
            $fail(__('playlist.time_overlap_error', [
                        'playlist_type' => __('playlist.time_overlap_error_type.' . $overlap->playlist_type),
                        'playlist_name' => $overlap->name,
                        'start_time' => $overlap->start_time,
                        'end_time' => $overlap->end_time
                    ]
                )
            );
    }
}
