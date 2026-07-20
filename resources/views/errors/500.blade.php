@extends('errors.layout')

@section('title', 'Erreur serveur')
@section('code', '500')

@section('scripts')
@php
    use App\Helpers\ErrorHelper;
    $errorData = ErrorHelper::getErrorMessage(500, $exception ?? null);
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