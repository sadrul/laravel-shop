<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Thank You for Your Order!</h3>
                        <p class="text-gray-600">Your order has been placed successfully.</p>
                    </div>

                    <div class="border-t pt-6">
                        <h4 class="text-lg font-semibold mb-4">Order Details</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Total:</span>
                                <span class="font-semibold">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Status:</span>
                                <span class="font-semibold">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-lg font-semibold mb-4">Order Items</h4>
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                        </div>
                                        <p class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center space-x-4">
                        <a href="{{ route('orders.show', $order) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Order Details
                        </a>
                        <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 