@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p class="text-center">
                            @if(auth()->user()->is_stripe_connected)
                                <a href="{{ route('stripe.connect') }}" class="btn btn-success">
                                    Stripe Connected
                                </a>
                            @else
                                <a href="{{ route('stripe.connect') }}" class="btn btn-primary">
                                    Connect your stripe connect account
                                </a>
                            @endif
                            <br>
                            <a href="{{ route('make-payment') }}" class="btn btn-outline-primary mt-3">
                                Make Payment
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
