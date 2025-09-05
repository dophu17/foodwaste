@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">👁️ {{ __('messages.Waste Record Details') }}</h1>
                    <p class="page-subtitle">{{ __('messages.View detailed information about this waste record') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('waste-records.edit', $wasteRecord) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>{{ __('messages.Edit') }}
                    </a>
                    <a href="{{ route('waste-records.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Waste Records') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Waste Record Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-hamburger me-2"></i>{{ __('messages.Food Item Details') }}
                                    </h6>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Food Item') }}:</strong><br>
                                        <span class="text-dark">{{ $wasteRecord->foodItem->name }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Menu') }}:</strong><br>
                                        <span class="text-muted">{{ $wasteRecord->foodItem->menu->name }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Restaurant') }}:</strong><br>
                                        <span class="text-muted">{{ $wasteRecord->foodItem->menu->restaurant->name }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Original Price') }}:</strong><br>
                                        <span class="text-success">{{ \App\Helpers\CurrencyHelper::format($wasteRecord->foodItem->price) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3">
                                    <h6 class="text-danger mb-3">
                                        <i class="fas fa-trash-alt me-2"></i>{{ __('messages.Waste Details') }}
                                    </h6>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Waste Date') }}:</strong><br>
                                        <span class="badge bg-info">{{ $wasteRecord->waste_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Quantity Wasted') }}:</strong><br>
                                        <span class="text-danger fw-bold">
                                            {{ number_format($wasteRecord->quantity_wasted, 2) }} {{ $wasteRecord->waste_unit }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Cost Wasted') }}:</strong><br>
                                        <span class="text-danger fw-bold fs-5">
                                            {{ \App\Helpers\CurrencyHelper::format($wasteRecord->cost_wasted) }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>{{ __('messages.Waste Reason') }}:</strong><br>
                                        <span class="badge bg-warning text-dark">{{ __('messages.' . $wasteRecord->waste_reason) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- AI Analysis Section -->
                        @if($wasteRecord->ai_predicted_waste || $wasteRecord->actual_waste_percentage || $wasteRecord->prediction_accuracy || $wasteRecord->ai_insights)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-robot me-2"></i>{{ __('messages.AI Analysis') }}
                                    </h6>
                                    <div class="row">
                                        @if($wasteRecord->ai_predicted_waste)
                                        <div class="col-md-3 mb-3">
                                            <div class="text-center">
                                                <strong class="text-info">{{ __('messages.AI Predicted') }}</strong><br>
                                                <span class="fs-5">{{ number_format($wasteRecord->ai_predicted_waste, 2) }} {{ $wasteRecord->waste_unit }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($wasteRecord->actual_waste_percentage)
                                        <div class="col-md-3 mb-3">
                                            <div class="text-center">
                                                <strong class="text-warning">{{ __('messages.Actual Waste %') }}</strong><br>
                                                <span class="fs-5">{{ number_format($wasteRecord->actual_waste_percentage, 1) }}%</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($wasteRecord->prediction_accuracy)
                                        <div class="col-md-3 mb-3">
                                            <div class="text-center">
                                                <strong class="text-success">{{ __('messages.Prediction Accuracy') }}</strong><br>
                                                <span class="fs-5">{{ number_format($wasteRecord->prediction_accuracy, 1) }}%</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($wasteRecord->ai_insights && count($wasteRecord->ai_insights) > 0)
                                        <div class="col-md-3 mb-3">
                                            <div class="text-center">
                                                <strong class="text-primary">{{ __('messages.AI Insights') }}</strong><br>
                                                <span class="fs-5">{{ count($wasteRecord->ai_insights) }} {{ __('messages.recommendations') }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($wasteRecord->ai_insights && count($wasteRecord->ai_insights) > 0)
                                    <div class="mt-3">
                                        <h6 class="text-secondary">{{ __('messages.AI Recommendations') }}:</h6>
                                        <ul class="list-unstyled">
                                            @foreach($wasteRecord->ai_insights as $insight)
                                            <li class="mb-1">
                                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                                {{ $insight }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($wasteRecord->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h6 class="text-secondary mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>{{ __('messages.Notes') }}
                                    </h6>
                                    <p class="mb-0">{{ $wasteRecord->notes }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h6 class="text-info mb-3">
                                        <i class="fas fa-clock me-2"></i>{{ __('messages.Record Timeline') }}
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>{{ __('messages.Created') }}:</strong><br>
                                                <small class="text-muted">{{ $wasteRecord->created_at->format('M d, Y H:i:s') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong>{{ __('messages.Last Updated') }}:</strong><br>
                                                <small class="text-muted">{{ $wasteRecord->updated_at->format('M d, Y H:i:s') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <strong>{{ __('messages.Record ID') }}:</strong><br>
                                        <small class="text-muted">#{{ $wasteRecord->id }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>{{ __('messages.Waste Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>{{ __('messages.Waste Percentage') }}:</span>
                                <strong class="text-danger">
                                    {{ number_format(($wasteRecord->quantity_wasted / max($wasteRecord->foodItem->stock_quantity, 1)) * 100, 1) }}%
                                </strong>
                            </div>
                                                            <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-danger" 
                                         style="width: {{ min(($wasteRecord->quantity_wasted / max($wasteRecord->foodItem->stock_quantity, 1)) * 100, 100) }}%"></div>
                                </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>{{ __('messages.Cost per Unit') }}:</span>
                                <strong class="text-info">
                                    {{ \App\Helpers\CurrencyHelper::format($wasteRecord->cost_wasted / $wasteRecord->quantity_wasted) }}/{{ $wasteRecord->waste_unit }}
                                </strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>{{ __('messages.Original vs Wasted') }}:</span>
                                <strong class="text-warning">
                                    {{ number_format(($wasteRecord->cost_wasted / $wasteRecord->foodItem->price) * 100, 1) }}%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>{{ __('messages.Actions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('waste-records.edit', $wasteRecord) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Record') }}
                            </a>
                            
                            <form action="{{ route('waste-records.destroy', $wasteRecord) }}" method="POST" 
                                  onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this waste record?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i>{{ __('messages.Delete Record') }}
                                </button>
                            </form>
                            
                            <a href="{{ route('waste-records.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list me-2"></i>{{ __('messages.Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>{{ __('messages.Insights') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>{{ __('messages.Waste Insights') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('messages.This waste represents a significant cost') }}</li>
                                <li>{{ __('messages.Consider reviewing preparation quantities') }}</li>
                                <li>{{ __('messages.Monitor this item for future waste patterns') }}</li>
                                <li>{{ __('messages.Review reason and implement preventive measures') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection