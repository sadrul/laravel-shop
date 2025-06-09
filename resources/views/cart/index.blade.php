<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(count($cart) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $total = 0;
                            @endphp

                            @foreach($cart as $id => $details)
                                @php
                                    $subtotal = $details['price'] * $details['quantity'];
                                    $total += $subtotal;
                                @endphp

                                <div class="flex items-center justify-between border-b pb-4">
                                    <div class="flex items-center space-x-4">
                                        @if($details['image'])
                                            <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="w-20 h-20 object-cover">
                                        @else
                                            <div class="w-20 h-20 bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-400">No image</span>
                                            </div>
                                        @endif

                                        <div>
                                            <h3 class="text-lg font-semibold">{{ $details['name'] }}</h3>
                                            <p class="text-gray-600">${{ number_format($details['price'], 2) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" class="w-16 border rounded px-2 py-1">
                                            <button type="submit" class="ml-2 text-blue-500 hover:text-blue-700">Update</button>
                                        </form>

                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                                        </form>

                                        <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex justify-between items-center pt-4">
                                <div class="text-xl font-bold">
                                    Total: ${{ number_format($total, 2) }}
                                </div>

                                <div class="space-x-4">
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Clear Cart
                                        </button>
                                    </form>

                                    <a href="{{ route('checkout.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-600 mb-4">Your cart is empty.</p>
                        <a href="{{ route('products.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 