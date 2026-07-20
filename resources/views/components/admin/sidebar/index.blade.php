{{--
    Admin Slidebar - Structure Preline UI avec données dynamiques
    Compatible avec App\View\Components\Admin\Slidebar
--}}

<!-- Navigation Toggle -->
<div class="lg:hidden py-16 text-center">
    <button type="button"
        class="py-2 px-3 inline-flex justify-center items-center gap-x-2 text-start bg-admin-accent border border-admin-accent text-white text-sm font-medium rounded-lg shadow-2xs align-middle hover:bg-admin-accent/90 focus:outline-hidden focus:bg-admin-accent/90"
        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-sidebar-collapsible-group"
        aria-label="Toggle navigation" data-hs-overlay="#hs-sidebar-collapsible-group">
        Menu
    </button>
</div>

<!-- Sidebar -->
<div id="hs-sidebar-collapsible-group"
    class="hs-overlay [--auto-close:lg]
            hs-overlay-minified:w-13
            lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-64
            hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform h-full hidden
            fixed top-0 start-0 bottom-0 z-60 bg-white border-e border-gray-200"
    role="dialog" tabindex="-1" aria-label="Sidebar">

    <div class="relative flex flex-col h-full max-h-full">
        <!-- Header -->
        <header class="py-4 px-2 flex justify-between items-center gap-x-2">
            <a class="flex-none font-semibold text-xl text-black focus:outline-hidden focus:opacity-80  hs-overlay-minified:hidden"
                href="{{ route('admin.dashboard') }}" aria-label="Brand">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-admin-accent rounded-lg flex items-center justify-center">
                        <x-brand-logo size="sm" color="white" />
                    </div>
                    <span class="text-lg font-bold text-gray-900">Admin</span>
                </div>
            </a>

            <!-- Close Button Mobile -->
            <div class="lg:hidden -me-2">
                <button type="button"
                    class="flex justify-center items-center gap-x-3 size-6 bg-white border border-gray-200 text-sm text-gray-600 hover:bg-gray-100 rounded-full focus:outline-hidden focus:bg-gray-100"
                    data-hs-overlay="#hs-sidebar-collapsible-group">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>

            <div class="hidden lg:block">
                <!-- Toggle Button -->
                <button type="button"
                    class="flex justify-center items-center flex-none gap-x-3 size-9 text-sm text-admin-600 hover:bg-admin-100 rounded-full disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100"
                    aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-sidebar-collapsible-group"
                    aria-label="Minify navigation" data-hs-overlay-minifier="#hs-sidebar-collapsible-group">
                    <svg class="hidden hs-overlay-minified:block shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M15 3v18" />
                        <path d="m8 9 3 3-3 3" />
                    </svg>
                    <svg class="hs-overlay-minified:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M15 3v18" />
                        <path d="m10 15-3-3 3-3" />
                    </svg>
                    <span class="sr-only">Navigation Toggle</span>
                </button>
                <!-- End Toggle Button -->
            </div>

        </header>

        <!-- Body -->
        <nav
            class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
            <div class="hs-accordion-group pb-0 px-2 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="space-y-1">

                    @foreach ($menus as $menu)
                        {{-- Divider --}}
                        @if (isset($menu['isDivider']) && $menu['isDivider'])
                            <li class="my-4">
                                <hr class="border-gray-200">
                            </li>

                            {{-- Menu Simple (sans subMenus) --}}
                        @elseif(!isset($menu['subMenus']) || empty($menu['subMenus']))
                            <li>
                                <a class="w-full flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 hs-overlay-minified:text-center {{ $menu['isActive'] ? 'bg-gray-100 text-gray-900' : '' }}"
                                    href="{{ $menu['link'] }}">
                                    <x-icon :name="$menu['icon']" class="shrink-0 size-4" />
                                    <span class="hs-overlay-minified:hidden transition-all duration-300">{{ $menu['title'] }}</span>
                                </a>
                            </li>

                            {{-- Menu avec SubMenus --}}
                        @else
                            <li class="hs-accordion {{ $menu['isOpen'] ? 'hs-accordion-active' : '' }}"
                                id="accordion-{{ $loop->index }}">
                                <button type="button"
                                    class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 hs-overlay-minified:text-center {{ $menu['isActive'] ? 'bg-gray-100 text-gray-900' : '' }}"
                                    aria-expanded="{{ $menu['isOpen'] ? 'true' : 'false' }}"
                                    aria-controls="accordion-collapse-{{ $loop->index }}">
                                    <x-icon :name="$menu['icon']" class="shrink-0 size-4" />
                                    <span class="hs-overlay-minified:hidden transition-all duration-300">{{ $menu['title'] }}</span>

                                    <svg class="hs-accordion-active:block hs-overlay-minified:hidden ms-auto hidden size-4"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m18 15-6-6-6 6" />
                                    </svg>

                                    <svg class="hs-accordion-active:hidden hs-overlay-minified:hidden ms-auto block size-4"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>

                                {{-- SubMenus --}}
                                <div id="accordion-collapse-{{ $loop->index }}"
                                    class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ $menu['isOpen'] ? '' : 'hidden' }}"
                                    role="region" aria-labelledby="accordion-{{ $loop->index }}">
                                    <ul class="pt-2 ps-2">
                                        @foreach ($menu['subMenus'] as $subMenu)
                                            {{-- <li class="hs-tooltip [--placement:left] inline-block"> --}}
                                            <li class="hs-tooltip [--placement:left] inline-block">
                                                <a class="flex items-center gap-x-3.5 py-2 px-2.5  text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 {{ $subMenu['isActive'] ? 'bg-gray-100 text-gray-900 font-medium' : '' }}"
                                                    href="{{ $subMenu['link'] }}">
                                                    <x-icon :name="$subMenu['icon']" class="shrink-0 size-3" />

                                                    <span class="hs-overlay-minified:hidden transition-all duration-300">{{ $subMenu['title'] }}</span>
                                                    {{-- <span
                                                        class="hidden hs-overlay-minified:block hs-tooltip-content hs-tooltip-shown:opacity-100  w-full hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700"
                                                        role="tooltip">
                                                        {{ $subMenu['title'] }}
                                                    </span> --}}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @endforeach

                </ul>
            </div>
        </nav>
    </div>
</div>
