@extends('errors.layout')

@section('title', 'Accès interdit')
@section('code', '403')

@section('scripts')
@php
    use App\Helpers\ErrorHelper;
    $errorData = ErrorHelper::getErrorMessage(403, $exception ?? null);
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