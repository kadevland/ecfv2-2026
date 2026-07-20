@extends('errors.layout')

@section('title', 'Session expirée')
@section('code', '419')

@section('scripts')
@php
    use App\Helpers\ErrorHelper;
    $errorData = ErrorHelper::getErrorMessage(419, $exception ?? null);
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