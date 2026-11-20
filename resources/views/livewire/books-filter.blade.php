<main class="py-8 sm:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ __('books.heading') }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">

            <!-- Left content (Filters) -->
            <aside class="col-span-1" x-data="{ filtersOpen: false }">
                <div class="bg-white p-4 sm:p-6 rounded-lg border border-gray-200 shadow-md">
                    <button type="button" @click="filtersOpen = ! filtersOpen" :aria-expanded="filtersOpen.toString()"
                        class="w-full flex items-center justify-between text-start lg:cursor-default lg:pointer-events-none lg:border-b lg:border-gray-200 lg:pb-4">
                        <span class="text-xl font-semibold text-gray-800">{{ __('books.filter_heading') }}</span>
                        <svg class="h-5 w-5 text-gray-500 transition-transform lg:hidden" :class="filtersOpen && 'rotate-180'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="filtersOpen" class="mt-6 lg:mt-8 space-y-8 lg:!block">

                    <div>
                        <h5 class="font-semibold text-gray-800 mb-3">{{ __('books.filter_categories') }}</h5>
                        <select name="category" wire:model.live="category"
                            class="w-full bg-gray-50 border border-gray-300 rounded-md text-gray-800 focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5">
                            <option value="">{{ __('books.filter_all') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{$category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <h5 class="font-semibold text-gray-800 mb-3">{{ __('books.filter_language') }}</h5>
                        <div class="space-y-2">
                            <label class="flex items-center text-gray-600 cursor-pointer">
                                <input type="radio" wire:model.live="language"
                                    value=""
                                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ms-2">{{ __('books.filter_all_language') }}</span>
                            </label>
                            @foreach ($languages as $language)
                                <label class="flex items-center text-gray-600 cursor-pointer">
                                    <input type="radio" wire:model.live="language"
                                        value="{{ $language->id }}"
                                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ms-2">{{ $language->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h5 class="font-semibold text-gray-800 mb-3">{{ __('books.type') }}</h5>
                        <div class="space-y-2">
                            <label class="flex items-center text-gray-600 cursor-pointer">
                                <input type="radio" wire:model.live="type" value=""
                                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ms-2">{{ __('books.filter_all') }}</span>
                            </label>
                            @foreach ($types as $type)
                                <label class="flex items-center text-gray-600 cursor-pointer">
                                    <input type="radio" wire:model.live="type" value="{{ $type->id }}"
                                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ms-2">{{ $type->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    </div>

                </div>
            </aside>



            <!-- Right content (Book List) -->
                <div class="col-span-1 lg:col-span-3">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <span class="text-gray-600">{{ __('books.found_count', ['count' => $booksCount]) }}</span>
                        <div class="flex items-center w-full sm:w-auto">
                            <span class="text-gray-600 me-2 shrink-0">{{ __('books.sort_label') }}</span>
                            <select name="sort"  wire:model.live="sort"
                                class="w-full sm:w-auto bg-gray-50 border border-gray-300 rounded-md text-gray-800 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                                <option value="sort_newest">
                                    {{ __('books.sort_newest') }}</option>
                                <option
                                    value="sort_oldest">
                                    {{ __('books.sort_oldest') }}</option>
                                <option
                                    value="sort_title">
                                    {{ __('books.sort_title') }}</option>
                                <option
                                    value="sort_price_high_low">
                                    {{ __('books.sort_price_high_low') }}</option>
                                <option
                                    value="sort_price_low_high">
                                    {{ __('books.sort_price_low_high') }}</option>
                                <option
                                    value="sort_author_asc">
                                    {{ __('books.sort_author_asc') }}</option>
                                <option
                                    value="sort_author_desc">
                                    {{ __('books.sort_author_desc') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- filtered books section --}}
                    <div class="space-y-4 sm:space-y-6">

                        @foreach ($books as $book)
                            <div
                                class="bg-white rounded-lg p-3 sm:p-4 flex items-center justify-between gap-3 sm:gap-4 border border-gray-200 shadow-sm hover:shadow-lg transition">
                                <div class="flex items-center min-w-0">
                                    <img src="{{ $book->cover_url }}" alt="Book Cover"
                                        class="w-14 sm:w-20 h-auto shrink-0 object-cover rounded-md me-3 sm:me-4">
                                    <div class="min-w-0">
                                        <a href="{{ route('book.show', $book) }}"
                                            class="block text-base sm:text-lg font-semibold text-gray-900 hover:text-blue-600 break-words">{{ $book->designation }}
                                        </a>
                                        <ul class="flex flex-wrap gap-x-4 text-sm text-gray-500 mt-1">
                                            <li>
                                                @foreach ($book->categories as $category )
                                                    {{ $category->name }}
                                                @endforeach    
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-end shrink-0">
                                    <a href="{{ route('book.show', $book) }}"
                                        class="text-sm sm:text-base text-blue-600 hover:underline">{{ __('books.details') }}</a>
                                    <span class="block text-base sm:text-lg font-semibold text-gray-900 mt-1">{{ $book->prix }}
                                    </span>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    @if ($books->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $books->links() }}
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</main>
