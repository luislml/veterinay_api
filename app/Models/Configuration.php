<?php

namespace App\Models;
use App\Traits\HasFiles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory; use HasFiles;

    protected $fillable = [
        'veterinary_id',
        'color_primary',
        'color_secondary',
        'about_us',
        'description_team',
        'phone',
        'phone_emergency',
    ];
    protected $table = 'configuration';

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
    public function favicon()
    {
        return $this->morphMany(File::class, 'fileable')
                    ->where('type', 'like', 'image/%');
    }
}
