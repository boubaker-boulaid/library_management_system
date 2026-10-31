<x-mail::message>
# {{ __('email.subtitle') }}

@if ($book->cover && $book->cover !== \App\Services\BookCoverStorage::PLACEHOLDER)
<img src="{{ $book->cover_url }}" alt="{{ $book->designation }}" width="192" style="display: block; margin: 0 auto 24px; border-radius: 8px;">
@endif

## {{ $book->designation }}

{{ $book->auteur }}

<x-mail::table>
| | |
|:--|:--|
| **{{ __('email.category') }}** | {{ $book->categories->pluck('name')->join(', ') ?: '—' }} |
| **{{ __('email.language') }}** | {{ $book->language?->name ?? '—' }} |
| **{{ __('email.type') }}** | {{ $book->type?->name ?? '—' }} |
| **{{ __('email.publisher') }}** | {{ $book->editeur ?: '—' }} |
@if ($book->annee)
| **{{ __('email.year') }}** | {{ $book->annee->format('Y') }} |
@endif
| **{{ __('email.price') }}** | {{ number_format((float) $book->prix, 2) }} {{ __('email.currency') }} |
</x-mail::table>

@if ($book->description)
**{{ __('email.description') }}**

{{ $book->description }}
@endif

<x-mail::button :url="route('book.show', $book)">
{{ __('email.view_book') }}
</x-mail::button>

<small>{{ __('email.disclaimer') }}</small>
</x-mail::message>
