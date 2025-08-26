<nav aria-label="breadcrumb" class="bg-light py-2">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-1"></i>Inicio
                </a>
            </li>
            
            @if(!empty($breadcrumbs))
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $breadcrumb['title'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
            
            @if($page && empty($breadcrumbs))
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $page }}
                </li>
            @endif
        </ol>
    </div>
</nav>