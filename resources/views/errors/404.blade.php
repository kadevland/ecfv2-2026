@extends('errors.layout')

@section('title', 'Page non trouvée')
@section('code', '404')

@section('scripts')
@php
    use App\Helpers\ErrorHelper;
    $errorData = ErrorHelper::getErrorMessage(404, $exception ?? null);
@endphp

{!! ErrorHelper::generateSweetAlertScript($errorData) !!}

<script>
// Masquer le placeholder dès que SweetAlert est prêt
document.addEventListener('DOMContentLoaded', function() {
    const placeholder = document.getElementById('loading-placeholder');
    if (placeholder) {
        placeholder.style.display = 'none';
    }
});
</script>
@endsection