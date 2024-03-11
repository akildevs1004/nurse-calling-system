<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomSubCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the room_category that owns the RoomSubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room_category()
    {
        return $this->belongsTo(RoomCategory::class);
    }
}
