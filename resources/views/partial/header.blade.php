@php
    $links = [
        ['route' => 'index', 'label' => __('layout.nav_home')],
        ['route' => 'book.index', 'label' => __('layout.nav_books')],
        ['route' => 'books', 'label' => __('layout.nav_search')],
        ['route' => 'about', 'label' => __('layout.nav_about')],
        ['route' => 'contact', 'label' => __('layout.nav_contact')],
    ];
@endphp

<header x-data="{ open: false }" @keydown.escape.window="open = false"
    class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
    <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 h-16">
            <div class="flex items-center min-w-0">
                <a href="{{ route('index') }}" class="shrink-0 text-gray-800 font-bold text-xl">{{ __('layout.brand') }}</a>

                <div class="hidden lg:flex items-center gap-1 ms-8">
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}"
                            @class([
                                'px-3 py-2 rounded-md text-sm font-medium',
                                'bg-gray-100 text-gray-900' => request()->routeIs($link['route']),
                                'text-gray-600 hover:bg-gray-100' => ! request()->routeIs($link['route']),
                            ])>{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <form method="POST" id="lang-form">
                    @csrf
                    <label for="lang-select" class="sr-only">Language</label>
                    <select id="lang-select" name="local"
                        onchange="this.form.action=`/language/${this.value}`; this.form.submit();"
                        class="py-1.5 ps-2 pe-8 rounded border border-gray-300 bg-white text-gray-700 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer hover:bg-gray-50 transition-colors">
                        <option value="en" {{ session('locale') == 'en' ? 'selected' : '' }}>@lang('layout.lang_en')</option>
                        <option value="fr" {{ session('locale') == 'fr' ? 'selected' : '' }}>@lang('layout.lang_fr')</option>
                        <option value="ar" {{ session('locale') == 'ar' ? 'selected' : '' }}>@lang('layout.lang_ar')</option>
                    </select>
                </form>

                <div class="hidden lg:flex items-center gap-2">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                                    <span class="max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                                    <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">{{ __('layout.nav_dashboard') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">{{ __('layout.nav_profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('layout.nav_logout') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <a href="{{ route('register') }}"
                            class="text-gray-600 hover:bg-gray-100 font-medium rounded-md text-sm px-3 py-2">{{ __('layout.nav_register') }}</a>
                        <a href="{{ route('login') }}"
                            class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-md text-sm px-3 py-2">{{ __('layout.nav_login') }}</a>
                    @endauth
                </div>

                <button type="button" @click="open = ! open" :aria-expanded="open.toString()" aria-controls="mobile-menu"
                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="sr-only">Menu</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path x-show="! open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity @click.outside="open = false"
            class="lg:hidden border-t border-gray-200 py-3">
            <div class="space-y-1">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                        @class([
                            'block px-3 py-2 rounded-md text-base font-medium',
                            'bg-gray-100 text-gray-900' => request()->routeIs($link['route']),
                            'text-gray-600 hover:bg-gray-100' => ! request()->routeIs($link['route']),
                        ])>{{ $link['label'] }}</a>
                @endforeach
            </div>

            <div class="mt-3 pt-3 border-t border-gray-200">
                @auth
                    <div class="px-3 mb-2">
                        <div class="font-medium text-base text-gray-800 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">{{ __('layout.nav_dashboard') }}</a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">{{ __('layout.nav_profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-start px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">{{ __('layout.nav_logout') }}</button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('register') }}"
                            class="text-center text-gray-700 border border-gray-300 hover:bg-gray-100 font-medium rounded-md text-sm px-3 py-2">{{ __('layout.nav_register') }}</a>
                        <a href="{{ route('login') }}"
                            class="text-center text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-md text-sm px-3 py-2">{{ __('layout.nav_login') }}</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>
