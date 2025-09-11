<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KurtiGroup extends Model
{
    use HasFactory;

    protected $fillable = ['bulan', 'pekan'];

    public function kurtis()
    {
        return $this->hasMany(Kurti::class);
    }
}
