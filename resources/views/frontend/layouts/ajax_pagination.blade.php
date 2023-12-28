<div class="shop-pagination">
    <ul class="pagination">


        @if ($paginator->hasPages())

            @if($paginator->currentPage()==1)
                <li class="page-item first"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()}}" class="page-link clickPage"><i class="fas fa-chevron-left"
                                                                                                                                                   ></i></a>
                </li>
                <li class="page-item prev"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()}}" class="page-link clickPage">Previous</a></li>
            @else
                <li class="page-item first"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()-1}}" class="page-link clickPage"><i class="fas fa-chevron-left"
                                                                                                                                                      ></i></a>
                </li>
                <li class="page-item prev"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()-1}}" class="page-link clickPage">Previous</a></li>
            @endif
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><a href="javascript:void(0);" data-page="{{ $page }}" class="page-link clickPage">{{$page}}</a></li>
                        @else
                            <li class="page-item"><a href="javascript:void(0);" data-page="{{ $page }}" class="page-link clickPage">{{$page}}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li class="page-item next"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()+1 }}" class="page-link clickPage">Next</a></li>
                <li class="page-item last"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage()+1 }}" class="page-link clickPage"><i class="fas fa-chevron-right"
                                                                                                                                                     ></i></a>
                </li>
            @else
                <li class="page-item next"><a href="javascript:void(0);" data-page="{{ $paginator->currentPage() }}" class="clickPage">Next</a></li>
                <li class="page-item last"><a  href="javascript:void(0);" data-page="{{ $paginator->currentPage() }}" class="page-link clickPage"><i class="fas fa-chevron-right"
                                                                                                                                                     ></i></a>
                </li>

            @endif

        @endif
    </ul>
</div>