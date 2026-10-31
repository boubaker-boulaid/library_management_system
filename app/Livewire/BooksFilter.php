<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BooksFilter extends Component
{
    use WithPagination;

    /**
     * Allowed sort options mapped to [column, direction].
     */
    private const SORTS = [
        'sort_newest' => ['created_at', 'desc'],
        'sort_oldest' => ['created_at', 'asc'],
        'sort_title' => ['designation', 'asc'],
        'sort_price_high_low' => ['prix', 'desc'],
        'sort_price_low_high' => ['prix', 'asc'],
        'sort_author_asc' => ['auteur', 'asc'],
        'sort_author_desc' => ['auteur', 'desc'],
    ];

    #[Url(except: 'sort_newest')]
    public string $sort = 'sort_newest';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $language = '';

    #[Url(except: '')]
    public string $type = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['sort', 'category', 'language', 'type'], true)) {
            $this->resetPage();
        }
    }

    #[Computed(persist: true)]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }

    #[Computed(persist: true)]
    public function languages(): Collection
    {
        return Language::orderBy('name')->get(['id', 'name']);
    }

    #[Computed(persist: true)]
    public function types(): Collection
    {
        return Type::orderBy('name')->get(['id', 'name']);
    }

    public function render()
    {
        [$column, $direction] = self::SORTS[$this->sort] ?? self::SORTS['sort_newest'];

        $books = Book::query()
            ->with('categories:id,name')
            ->when($this->category !== '', fn ($query) => $query->whereHas(
                'categories',
                fn ($query) => $query->whereKey((int) $this->category)
            ))
            ->when($this->language !== '', fn ($query) => $query->where('language_id', (int) $this->language))
            ->when($this->type !== '', fn ($query) => $query->where('type_id', (int) $this->type))
            ->orderBy($column, $direction)
            ->orderBy('id', $direction)
            ->paginate(4);

        return view('livewire.books-filter', [
            'books' => $books,
            'booksCount' => $books->total(),
            'categories' => $this->categories,
            'languages' => $this->languages,
            'types' => $this->types,
        ]);
    }
}
