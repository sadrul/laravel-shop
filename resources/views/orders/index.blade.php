<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Order #{{ $order->order_number }}</h3>
                                            <p class="text-gray-600">{{ $order->created_at->format('F j, Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold">${{ number_format($order->total_amount, 2) }}</p>
                                            <span class="inline-block px-2 py-1 text-sm rounded
                                                @if($order->status === 'completed') bg-green-100 text-green-800
                                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4">
                                        <h4 class="font-semibold mb-2">Order Items:</h4>
                                        <div class="space-y-2">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center space-x-4">
                                                        @if($item->product->image)
                                                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-200 flex items-center justify-center">
                                                                <span class="text-gray-400 text-xs">No image</span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="font-medium">{{ $item->product->name }}</p>
                                                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="font-semibold">${{ number_format($item->subtotal, 2) }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-4 text-right">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-500 hover:text-blue-700">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-600 mb-4">You haven't placed any orders yet.</p>
                        <a href="{{ route('products.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 