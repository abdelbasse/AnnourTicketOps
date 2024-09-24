<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'name',
        'isFile',
        'path',
        'extension',
        'parentId',
    ];

    // Define a relationship for parent folder if needed
    public function parent()
    {
        return $this->belongsTo(FileFolder::class, 'parentId');
    }

    // Define a relationship for children if needed
    public function children()
    {
        return $this->hasMany(FileFolder::class, 'parentId');
    }
}
