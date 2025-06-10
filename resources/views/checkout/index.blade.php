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
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Shipping Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" id="shipping_address" name="shipping_address" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="{{ auth()->user()->address ?? '' }}">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_city" class="block text-sm font-medium text-gray-700">City</label>
                                        <input type="text" id="shipping_city" name="shipping_city" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            value="{{ auth()->user()->city ?? '' }}">
                                    </div>
                                    <div>
                                        <label for="shipping_state" class="block text-sm font-medium text-gray-700">State</label>
                                        <input type="text" id="shipping_state" name="shipping_state" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            value="{{ auth()->user()->state ?? '' }}">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_zipcode" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                                        <input type="text" id="shipping_zipcode" name="shipping_zipcode" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            value="{{ auth()->user()->zipcode ?? '' }}">
                                    </div>
                                    <div>
                                        <label for="shipping_country" class="block text-sm font-medium text-gray-700">Country</label>
                                        <input type="text" id="shipping_country" name="shipping_country" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            value="{{ auth()->user()->country ?? '' }}">
                                    </div>
                                </div>
                                <div>
                                    <label for="shipping_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="tel" id="shipping_phone" name="shipping_phone" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Payment Information</h3>
                            <div id="payment-element" class="mb-6">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <button id="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Pay ${{ number_format($total, 2) }}
                            </button>
                            <div id="error-message" class="mt-4 text-red-500"></div>
                        </div>
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
                    // Validate shipping information
                    const shippingFields = [
                        'shipping_address',
                        'shipping_city',
                        'shipping_state',
                        'shipping_zipcode',
                        'shipping_country',
                        'shipping_phone'
                    ];

                    const shippingData = {};
                    let isValid = true;

                    shippingFields.forEach(field => {
                        const input = document.getElementById(field);
                        if (!input.value.trim()) {
                            input.classList.add('border-red-500');
                            isValid = false;
                        } else {
                            input.classList.remove('border-red-500');
                            shippingData[field] = input.value.trim();
                        }
                    });

                    if (!isValid) {
                        throw new Error('Please fill in all shipping information fields.');
                    }

                    // Store shipping information in session storage
                    sessionStorage.setItem('shipping_info', JSON.stringify(shippingData));

                    console.log('Starting payment process...');
                    console.log('Client secret:', clientSecret);
                    
                    // Confirm the payment and let Stripe handle the redirect
                    const { error: submitError } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: window.location.origin + '/checkout/success',
                        }
                    });

                    console.log('Payment confirmation result:', { submitError });

                    if (submitError) {
                        console.error('Payment error:', submitError);
                        const messageDiv = document.getElementById('error-message');
                        messageDiv.textContent = submitError.message;
                        messageDiv.style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay ${{ number_format($total, 2) }}';
                    }
                } catch (e) {
                    console.error('Payment processing error:', e);
                    const messageDiv = document.getElementById('error-message');
                    messageDiv.textContent = e.message || 'An error occurred while processing your payment. Please try again.';
                    messageDiv.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay ${{ number_format($total, 2) }}';
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 