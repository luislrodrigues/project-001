@if ($paginator->hasPages())
    <div class="flex items-end my-2">

        {{-- First Page Link --}}
        @if ( ! $paginator->onFirstPage())
            <a class="mx-1 px-3 py-1 bg-blue-900 border-2 border-blue-900 text-white font-bold text-center text-xs hover:bg-blue-400 hover:border-blue-400 rounded-lg cursor-pointer"
               wire:click="gotoPage(1)">
                <<
            </a>
            @if($paginator->currentPage() > 2)
                {{-- Previous Page Link --}}
                <a class="mx-1 px-3 py-1 bg-blue-900 border-2 border-blue-900 text-white font-bold text-center text-xs hover:bg-blue-400 hover:border-blue-400 rounded-lg cursor-pointer"
                   wire:click="previousPage">
                    <
                </a>
            @endif
        @endif

        <!-- Pagination Elements -->
        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    {{-- Show dots when current page is greater than 3 --}}
                    @if ($paginator->currentPage() > 3 && $page === 2)
                        <div class="text-blue-800 mx-1">
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                        </div>
                    @endif

                    {{-- Show active page two pages before and after it --}}
                    @if ($page == $paginator->currentPage())
                        <span class="mx-1 px-3 py-1 border-2 border-blue-400 bg-blue-400 text-white font-bold text-center text-xs hover:bg-blue-800 hover:border-blue-800 rounded-lg cursor-pointer">{{ $page }}</span>
                    @elseif ($page >= $paginator->currentPage() - 2 && $page <= $paginator->currentPage() + 2)
                        <a class="mx-1 px-3 py-1 border-2 border-blue-900 text-blue-900 font-bold text-center text-xs hover:text-blue-400 rounded-lg cursor-pointer" wire:click="gotoPage({{$page}})">{{ $page }}</a>
                    @endif

                    {{-- Show dots when current page is away from end --}}
                    @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                        <div class="text-blue-800 mx-1">
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            @if($paginator->lastPage() - $paginator->currentPage() >= 2)
                <a class="mx-1 px-3 py-1 bg-blue-900 border-2 border-blue-900 text-white font-bold text-center text-xs hover:bg-blue-400 hover:border-blue-400 rounded-lg cursor-pointer"
                   wire:click="nextPage" rel="next">
                    >
                </a>
            @endif
            <a class="mx-1 px-3 py-1 bg-blue-900 border-2 border-blue-900 text-white font-bold text-center text-xs hover:bg-blue-400 hover:border-blue-400 rounded-lg cursor-pointer"
               wire:click="gotoPage({{ $paginator->lastPage() }})">
                >>
            </a>
        @endif

    </div>
@endif
