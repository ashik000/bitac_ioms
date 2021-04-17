@extends('layouts.base')

@section('content')
    <header class="summary-panels">
        <div class="panel product-panel">
            <div class="row">
                <h1 class="col-6">
                    0
                    <small>{{ $product->unit }}</small>
                </h1>

                <h1 class="col-6 text-right"></h1>
            </div>

            <div class="row mt-3">
                <h3 class="col-6">{{ $product->name }}</h3>
                <h3 class="col-6 text-right">0/0</h3>
            </div>
        </div>

        <div class="panel station-panel">
            <h2>
                {{ $station->name }}
            </h2>
            <h4>
                {{ today()->format('D, d M Y') }} - {{ $shift->name }}
            </h4>

            <div class="row mt-3">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
        </div>

        <div class="panel meta-panel">
            <h1 class="text-center">
                {{ now()->toTimeString() }}
            </h1>
            <img class="img-fluid" src="{{ asset('images/inovace-logo.png') }}" alt="Inovace Technologies">
        </div>
    </header>

    <div class="table-responsive mt-4">
        <table class="production-summary table table-bordered">
            <thead>
            <tr>
                <th style="width: 5%">Hour</th>
                <th style="width: 85%">Availability</th>
                <th style="width: 10%">Performance</th>
            </tr>
            </thead>
            <tbody>
            @foreach($availabilityBars as $hour => $bars)
                <tr>
                    <td>{{ $hour }}</td>
                    <td>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                             viewBox="0 0 3600 10" style="background: #222056;" preserveAspectRatio="none">
                        @foreach($bars as $bar)
                            <!-- Log ID:  {{ $bar['meta']['log_id'] }} -->
                            <!-- Produced At:  {{ $bar['meta']['produced_at'] }} -->
                            <!-- Second In Hour:  {{ $bar['meta']['second_in_hour'] }} -->
                            <!-- Start Stop Time:  {{ $bar['meta']['stop_start_time'] }} -->
                            <!-- Product Cycle Time:  {{ $bar['meta']['product_cycle_time'] }} -->
                            <!-- Log Cycle Interval:  {{ $bar['meta']['log_cycle_interval'] }} -->
                                <rect x="{{ $bar['x'] }}" width="{{ $bar['width'] }}" y="0" height="10" fill="{{ $bar['color'] }}"></rect>
                            @endforeach
                        </svg>
                    </td>
                    <td class="d-flex justify-content-around">
                        @if (($producedAmounts[$hour] * 100 / $product->meta->units_per_hour) < $station->unhappy_performance)
                            <span class="text-danger text-right">{{ $producedAmounts[$hour] }}</span>
                        @else
                            <span>{{ $producedAmounts[$hour] }}</span>
                        @endif
                        <span class="text-center">/</span>
                        <span class="text-left">{{ $product->meta->units_per_hour }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
