<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = ['designation', 'description', 'prix', 'auteur', 'cover', 'type_id', 
                            'language_id','annee', 'editeur', 'categorie'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
