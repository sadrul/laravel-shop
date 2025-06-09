<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row gap-8">
                <div class="md:w-1/3 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-auto rounded">
                    @else
                        <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded">
                            <span class="text-gray-400 text-xs">No image</span>
                        </div>
                    @endif
                </div>
                <div class="md:w-2/3 flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-700 mb-4">{{ $product->description }}</p>
                        <p class="text-lg font-semibold mb-2">${{ number_format($product->price, 2) }}</p>
                        <p class="mb-4">
                            <span class="font-medium">Stock:</span>
                            @if($product->stock > 0)
                                <span class="text-green-600">{{ $product->stock }} available</span>
                            @else
                                <span class="text-red-600">Out of stock</span>
                            @endif
                        </p>
                    </div>
                    <div class="mt-4">
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-16 border rounded px-2 py-1">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button class="bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed" disabled>
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Products</a>
            </div>
        </div>
    </div>
</x-app-layout> 