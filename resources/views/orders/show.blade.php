<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-blue-500 hover:text-blue-700">
                Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Order Number:</span> {{ $order->order_number }}</p>
                                <p><span class="font-medium">Date:</span> {{ $order->created_at->format('F j, Y H:i:s') }}</p>
                                <p><span class="font-medium">Status:</span> 
                                    <span class="inline-block px-2 py-1 text-sm rounded
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Total Amount:</span> ${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Shipping Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $order->shipping_name }}</p>
                                <p><span class="font-medium">Address:</span> {{ $order->shipping_address }}</p>
                                <p><span class="font-medium">City:</span> {{ $order->shipping_city }}</p>
                                <p><span class="font-medium">State:</span> {{ $order->shipping_state }}</p>
                                <p><span class="font-medium">Postal Code:</span> {{ $order->shipping_postal_code }}</p>
                                <p><span class="font-medium">Country:</span> {{ $order->shipping_country }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($item->product->image)
                                                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded">
                                                    @else
                                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                            <span class="text-gray-400 text-xs">No image</span>
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($item->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($order->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 