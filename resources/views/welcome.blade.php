@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="chart">
                    <div class="chart__item">
                        <img src="{{ route('chart', ['field' => 'deaths']) }}" alt="ncov virus deaths chart">
                    </div>
                    <div class="chart__item">
                        <img src="{{ route('chart', ['field' => 'infected']) }}" alt="ncov virus infected chart">
                    </div>
                    <div class="chart__item">
                        <img src="{{ route('chart', ['field' => 'cured']) }}" alt="ncov virus cured chart">
                    </div>
                </div>
                </div>
        </div>
    </div>
    </div>
@endsection
