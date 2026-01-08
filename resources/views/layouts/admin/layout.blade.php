@include('components.shared.head')
@include('components.shared.top-bar')
@include('components.shared.left-sidebar')
<div class="wrapper">
@yield('content')
</div>
@include('components.shared.footer-scripts')
{{-- @include('components.shared.footer') --}}

