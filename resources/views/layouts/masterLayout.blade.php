@include('components.head')

@include('components.navbar')


<main id="main">
    {{-- Hero --}}
    @include('components.hero')
    {{-- Hero End --}}


    {{-- About --}}
    @include('components.about')
    {{-- About End --}}

    {{-- Categori --}}
    @include('components.categori')
    {{-- Categori End --}}

    {{-- Benefit --}}
    @include('components.benefit')
    {{-- Benefit End --}}

    {{-- Infographic --}}
    @include('components.infographic')
    {{-- Infographic End --}}

    {{-- Relevance --}}
    @include('components.relevance')
    {{-- Relevance End --}}

    {{-- Footer --}}
    @include('components.footer')
    {{-- Footer End --}}

</main>


@include('components.scripts')
