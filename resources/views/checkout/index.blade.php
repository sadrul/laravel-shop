<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                        <div class="space-y-2">
                            @foreach(session('cart', []) as $id => $details)
                                <div class="flex justify-between">
                                    <span>{{ $details['name'] }} x {{ $details['quantity'] }}</span>
                                    <span>${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="border-t pt-2 font-semibold">
                                <div class="flex justify-between">
                                    <span>Total:</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="payment-form" class="max-w-md mx-auto">
                        <div id="payment-element" class="mb-6">
                            <!-- Stripe Elements will be inserted here -->
                        </div>
                        <button id="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Pay ${{ number_format($total, 2) }}
                        </button>
                        <div id="error-message" class="mt-4 text-red-500"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        console.log('Checkout page script loaded');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded in checkout page');
            
            const stripeKey = '{{ config('services.stripe.key') }}';
            const clientSecret = '{{ $clientSecret }}';
            
            console.log('Stripe Key:', stripeKey);
            console.log('Client Secret:', clientSecret);
            
            if (!stripeKey || !clientSecret) {
                console.error('Missing Stripe configuration');
                document.getElementById('error-message').textContent = 'Payment system configuration error. Please try again later.';
                return;
            }

            const stripe = Stripe(stripeKey);
            console.log('Stripe initialized');

            const elements = stripe.elements({
                clientSecret,
                appearance: {
                    theme: 'stripe',
                }
            });
            console.log('Elements created');

            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
            console.log('Payment Element mounted');

            const form = document.getElementById('payment-form');
            console.log('Form found:', form);

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                console.log('Form submitted');

                const submitButton = document.getElementById('submit');
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';

                try {
                    console.log('Confirming payment...');
                    const { error, paymentIntent } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: window.location.origin + '/checkout/success',
                        },
                        redirect: 'if_required'
                    });

                    console.log('Payment result:', { error, paymentIntent });

                    if (error) {
                        console.error('Payment error:', error);
                        const errorElement = document.getElementById('error-message');
                        errorElement.textContent = error.message || 'An error occurred during payment.';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay ${{ number_format($total, 2) }}';
                    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                        console.log('Payment succeeded, redirecting...');
                        window.location.href = window.location.origin + '/checkout/success?payment_intent=' + paymentIntent.id;
                    } else {
                        console.log('Payment requires additional action');
                        const errorElement = document.getElementById('error-message');
                        errorElement.textContent = 'Please complete the payment process.';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay ${{ number_format($total, 2) }}';
                    }
                } catch (e) {
                    console.error('Unexpected error:', e);
                    const errorElement = document.getElementById('error-message');
                    errorElement.textContent = 'An unexpected error occurred. Please try again.';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay ${{ number_format($total, 2) }}';
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 