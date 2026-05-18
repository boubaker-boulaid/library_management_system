<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use Livewire\Component;
use Livewire\WithPagination;

class BooksFilter extends Component
{
    use WithPagination;
    public $sort = 'sort_newest';
    public $category = 'All';
    public $language = 'All';
    public $type = 'All';

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedLanguage()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Book::query();

        if ($this->sort === 'sort_newest') {
            $query->latest();
        } elseif ($this->sort === 'sort_oldest') {
            $query->oldest();
        } elseif ($this->sort === 'sort_title') {
            $query->orderBy('designation', 'asc');
        } elseif ($this->sort === 'sort_price_high_low') {
            $query->orderBy('prix', 'desc');
        } elseif ($this->sort === 'sort_price_low_high') {
            $query->orderBy('prix', 'asc');
        }   
        
        if ($this->category !== 'All') {
            $query->whereHas('categories', function ($q) {
                $q->where('name', $this->category);
            });
        }

        if ($this->language !== 'All') {
            $query->where('language_id', $this->language);
        }

        if ($this->type !== 'All') {
            $query->where('type_id', $this->type);
        }

        $books = $query->with('categories:id,name')->paginate(4);
        $categories = Category::latest()->get();
        $languages = Language::latest()->get();
        $types = Type::latest()->get();

        return view('livewire.books-filter', [
            'books' => $books,
            'booksCount' => $books->total(),
            'categories' => $categories,
            'languages' => $languages,
            'types' => $types
        ]);
    }
}
