@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🤖 AI Insights</h1>
            <p class="page-subtitle">Intelligent recommendations powered by artificial intelligence</p>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- AI Accuracy Overview -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🎯</div>
                    <div class="number">{{ number_format($aiAccuracy, 1) }}%</div>
                    <div class="label">AI Prediction Accuracy</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">💡</div>
                    <div class="number">{{ count($aiInsights) }}</div>
                    <div class="label">Active Insights</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">💰</div>
                    <div class="number">¥{{ number_format($totalWasteCost, 0) }}</div>
                    <div class="label">Total Waste Cost</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">📊</div>
                    <div class="number">{{ $wasteCount }}</div>
                    <div class="label">Waste Records</div>
                </div>
            </div>
        </div>

        <!-- Waste Analytics Overview -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>📈 Daily Waste Trend (This Month)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($dailyWaste->count() > 0)
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="dailyWasteChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-line fs-1 mb-3"></i>
                                <p>No daily waste data available for charting.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>🥧 Waste by Category
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($wasteByCategory->count() > 0)
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="categoryWasteChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-pie fs-1 mb-3"></i>
                                <p>No category waste data available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-robot me-2"></i>🤖 AI-Generated Insights & Recommendations
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($aiInsights))
                            <div class="row">
                                @foreach($aiInsights as $insight)
                                    <div class="col-lg-6 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        @switch($insight['type'])
                                                            @case('success')
                                                                <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                                                                @break
                                                            @case('warning')
                                                                <i class="fas fa-exclamation-triangle text-warning fs-4 me-3"></i>
                                                                @break
                                                            @case('danger')
                                                                <i class="fas fa-exclamation-circle text-danger fs-4 me-3"></i>
                                                                @break
                                                            @case('info')
                                                                <i class="fas fa-info-circle text-info fs-4 me-3"></i>
                                                                @break
                                                            @default
                                                                <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                                                        @endswitch
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-2">{{ $insight['title'] }}</h6>
                                                        <p class="card-text text-muted mb-0">{{ $insight['description'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-robot fs-1 mb-3"></i>
                                <p>No AI insights available yet. Continue using the system to generate personalized recommendations!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Waste Items -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>📊 Top Waste Items (This Month)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($topWasteItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Food Item</th>
                                            <th>Category</th>
                                            <th>Waste Cost</th>
                                            <th>Waste Quantity</th>
                                            <th>AI Prediction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topWasteItems as $index => $item)
                                            <tr>
                                                <td>
                                                    @if($index < 3)
                                                        <span class="badge bg-warning fs-6">#{{ $index + 1 }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-hamburger text-danger me-2"></i>
                                                        <strong>{{ $item->foodItem->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $item->foodItem->category }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        ¥{{ number_format($item->cost_wasted, 0) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ number_format($item->quantity_wasted, 1) }} {{ $item->waste_unit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($item->ai_predicted_waste)
                                                        <span class="text-muted">
                                                            {{ number_format($item->ai_predicted_waste, 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fs-1 mb-3"></i>
                                <p>No waste data available for analysis yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Learning Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-brain me-2"></i>🧠 AI Learning Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-database text-primary fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Data Collection</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" style="width: 75%">75%</div>
                                        </div>
                                        <small class="text-muted">Collecting more waste data to improve predictions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chart-line text-success fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Pattern Recognition</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 60%">60%</div>
                                        </div>
                                        <small class="text-muted">Learning seasonal and daily patterns</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-magic text-warning fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Recommendation Engine</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" style="width: 85%">85%</div>
                                        </div>
                                        <small class="text-muted">Providing actionable insights</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-robot text-info fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Overall AI Score</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: {{ $aiAccuracy }}%">{{ number_format($aiAccuracy, 0) }}%</div>
                                        </div>
                                        <small class="text-muted">Based on prediction accuracy</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add interactive features
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to insight cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add hover effects to table rows
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'all 0.2s ease';
            });

            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = 'scale(1)';
            });
        });

        // Add click effects to action buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });

        // Animate progress bars on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBar = entry.target.querySelector('.progress-bar');
                    if (progressBar) {
                        const width = progressBar.style.width;
                        progressBar.style.width = '0%';
                        setTimeout(() => {
                            progressBar.style.width = width;
                            progressBar.style.transition = 'width 1s ease-in-out';
                        }, 100);
                    }
                }
            });
        }, observerOptions);

        document.querySelectorAll('.progress').forEach(progress => {
            observer.observe(progress);
        });
    });
</script>
@endpush
