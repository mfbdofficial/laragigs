<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    public function scopeFilter($query, array $filters) {
        if($filters['tag'] ?? false) {
            $query->where('tags', 'LIKE', '%' . request('tag') . '%');
        }
        if($filters['search'] ?? false) {
            $query->where('title', 'LIKE', '%' . request('search') . '%')
                ->orWhere('description', 'LIKE', '%' . request('search') . '%')
                ->orWhere('tags', 'LIKE', '%' . request('search') . '%')
                ->orWhere('location', 'LIKE', '%' . request('search') . '%');
        }
    }

    //Relationship
    public function user() //kalo belongsTo(), nama method-nya dibuat singular saja
    {
        return $this->belongsTo(User::class, 'user_id'); //pakai belongsTo() karena konsep kita yaitu sebuah listing adalah kepunyaan (dibuat oleh) user tertentu
    }
}
