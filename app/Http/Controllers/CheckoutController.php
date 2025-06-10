<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        try {
            // Set your Stripe secret key
            Stripe::setApiKey(config('services.stripe.secret'));
            Log::info('Creating payment intent for amount: ' . $total);

            // Create a PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($total * 100), // amount in cents
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'order_id' => uniqid('order_'),
                ],
            ]);

            Log::info('Payment intent created successfully', [
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $total,
                'status' => $paymentIntent->status
            ]);

            return view('checkout.index', [
                'clientSecret' => $paymentIntent->client_secret,
                'total' => $total
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'type' => $e->getStripeErrorType()
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'There was an error processing your payment. Please try again.');
        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function success(Request $request)
    {
        Log::info('Checkout success page accessed', [
            'payment_intent' => $request->get('payment_intent'),
            'payment_intent_client_secret' => $request->get('payment_intent_client_secret'),
            'session_id' => session()->getId(),
            'cart' => session()->get('cart')
        ]);

        if (!$request->get('payment_intent')) {
            Log::warning('No payment intent found in request');
            return redirect()->route('cart.index')
                ->with('error', 'Invalid payment session. Please try again.');
        }

        try {
            // Verify the payment intent
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::retrieve($request->get('payment_intent'));

            Log::info('Payment intent retrieved', [
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'id' => $paymentIntent->id
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                Log::warning('Payment not succeeded', ['status' => $paymentIntent->status]);
                return redirect()->route('cart.index')
                    ->with('error', 'Payment was not successful. Please try again.');
            }

            // Check if order already exists for this payment intent
            $existingOrder = Order::where('payment_intent_id', $paymentIntent->id)->first();
            if ($existingOrder) {
                Log::info('Order already exists for payment intent', [
                    'order_id' => $existingOrder->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return view('checkout.success', compact('existingOrder'));
            }

            $cart = session()->get('cart', []);
            
            if (empty($cart)) {
                Log::warning('Empty cart on success page', [
                    'session_id' => session()->getId(),
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty. Please try again.');
            }

            DB::beginTransaction();

            try {
                // Get user's shipping information
                $user = auth()->user();
                
                // Create the order
                $order = new Order();
                $order->user_id = $user->id;
                $order->total_amount = $this->calculateTotal($cart);
                $order->status = 'completed';
                $order->payment_status = 'paid';
                $order->payment_intent_id = $paymentIntent->id;
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
                
                // Get shipping information from the request
                $shippingInfo = $request->get('shipping_info', []);
                
                // Set shipping information
                $order->shipping_address = $shippingInfo['shipping_address'] ?? $user->address ?? 'Default Address';
                $order->shipping_city = $shippingInfo['shipping_city'] ?? $user->city ?? 'Default City';
                $order->shipping_state = $shippingInfo['shipping_state'] ?? $user->state ?? 'Default State';
                $order->shipping_zipcode = $shippingInfo['shipping_zipcode'] ?? $user->zipcode ?? '00000';
                $order->shipping_country = $shippingInfo['shipping_country'] ?? $user->country ?? 'Default Country';
                $order->shipping_phone = $shippingInfo['shipping_phone'] ?? $user->phone ?? '000-000-0000';
                
                $order->save();

                Log::info('Order created', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                // Create order items
                foreach ($cart as $id => $details) {
                    $product = Product::findOrFail($id);
                    
                    // Check if enough stock is available
                    if ($product->stock < $details['quantity']) {
                        throw new \Exception("Not enough stock for {$product->name}");
                    }

                    // Calculate subtotal
                    $subtotal = $details['price'] * $details['quantity'];

                    // Create order item
                    $order->items()->create([
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'subtotal' => $subtotal
                    ]);

                    // Update product stock
                    $product->decrement('stock', $details['quantity']);

                    Log::info('Order item created', [
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'subtotal' => $subtotal
                    ]);
                }

                DB::commit();
                Log::info('Order completed successfully', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                // Clear the cart
                session()->forget('cart');

                return view('checkout.success', compact('order'));

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order Creation Error: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return redirect()->route('cart.index')
                    ->with('error', 'There was an error creating your order: ' . $e->getMessage());
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'type' => $e->getStripeErrorType(),
                'payment_intent_id' => $request->get('payment_intent')
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'There was an error verifying your payment. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_intent_id' => $request->get('payment_intent')
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
} 