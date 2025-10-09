@extends('layouts.app')

@section('title', __('common.settings'))

@section('main-section')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('common.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('common.settings') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('common.settings') }}</h4>
            </div>
        </div>
    </div>

    <!-- Settings Cards -->
    <div class="row">
        <!-- Rate Schemes Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-dollar-sign fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ __('settings.rate_schemes.page_title') }}</h5>
                    <p class="card-text">{{ __('settings.rate_schemes.description') }}</p>
                    <a href="{{ route('settings.rate-schemes.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>{{ __('common.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Languages Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-language fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">{{ __('settings.languages.page_title') }}</h5>
                    <p class="card-text">{{ __('settings.languages.description') }}</p>
                    <a href="{{ route('settings.languages.index') }}" class="btn btn-success">
                        <i class="fas fa-cog me-2"></i>{{ __('common.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Course Levels Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-layer-group fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">{{ __('settings.course_levels.page_title') }}</h5>
                    <p class="card-text">{{ __('settings.course_levels.description') }}</p>
                    <a href="{{ route('settings.course-levels.index') }}" class="btn btn-info">
                        <i class="fas fa-cog me-2"></i>{{ __('common.manage') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('settings.quick_stats') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-primary" id="rate-schemes-count">-</h3>
                                <p class="text-muted">{{ __('settings.rate_schemes.page_title') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success" id="languages-count">-</h3>
                                <p class="text-muted">{{ __('settings.languages.page_title') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-info" id="course-levels-count">-</h3>
                                <p class="text-muted">{{ __('settings.course_levels.page_title') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js-link')
<script>
$(document).ready(function() {
    // Load quick stats
    loadQuickStats();
});

function loadQuickStats() {
    // Load rate schemes count
    $.ajax({
        url: "{{ route('settings.rate-schemes.index') }}",
        type: 'GET',
        data: { ajax: true },
        success: function(response) {
            if (response.recordsTotal) {
                $('#rate-schemes-count').text(response.recordsTotal);
            }
        }
    });

    // Load languages count
    $.ajax({
        url: "{{ route('settings.languages.index') }}",
        type: 'GET',
        data: { ajax: true },
        success: function(response) {
            if (response.recordsTotal) {
                $('#languages-count').text(response.recordsTotal);
            }
        }
    });

    // Load course levels count
    $.ajax({
        url: "{{ route('settings.course-levels.index') }}",
        type: 'GET',
        data: { ajax: true },
        success: function(response) {
            if (response.recordsTotal) {
                $('#course-levels-count').text(response.recordsTotal);
            }
        }
    });
}
</script>
@endpush