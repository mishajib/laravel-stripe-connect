@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Make Payment') }}</div>

                    <div class="card-body">
                        <form action="{{ route('store-payment') }}" method="post" id="payment-form">
                            @csrf

                            <input type="number" min="1" name="amount" value="{{ old('amount', 100) }}" class="form-control mb-3"
                                   placeholder="Enter amount">

                            <input id="card-holder-name" name="name" type="text" class="form-control"
                                   placeholder="Enter card holder name"
                                   value="{{ auth()->user()->name ?? old('name') }}">

                            <!-- Stripe Elements Placeholder -->
                            <div id="card-element" class="mt-4"></div>

                            <hr/>
                            <div id="card-errors" class="mx-4"></div>
                            <div class="form-actions">
                                <button type="submit" id="card-button" ; const
                                        clientSecret=cardButton.dataset.secret;data-secret="{{ config('stripe.secret') }}"
                                        class="btn btn-primary btn-lg float-end mt-3"><span class="mr-2">Pay Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Create a Stripe client
        var stripe   = Stripe('{{ config('stripe.key') }}');
        // Create an instance of Elements
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '16px',
                color   : "#32325d",
            }
        };

        // Create an instance of the card Element
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    // Inform the user if there was an error
                    var errorElement         = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            var form        = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }

    </script>
@endpush
