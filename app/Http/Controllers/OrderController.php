<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get a list of orders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="Filter by start date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="Filter by end date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrderResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::with(['user', 'items.meal'])
            ->orderBy('placed_at', 'desc');

        // Filter by user
        if ($request->has('userId')) {
            $query->where('user_id', $request->userId);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('fromDate')) {
            $query->whereDate('placed_at', '>=', $request->fromDate);
        }
        if ($request->has('toDate')) {
            $query->whereDate('placed_at', '<=', $request->toDate);
        }

        $orders = $query->paginate($request->input('perPage', 15));

        return OrderResource::collection($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            // Create the order
            $order = Order::create([
                'user_id' => $request->userId,
                'total_cents' => 0, // Will be calculated
                'status' => 'pending',
                'delivery_address' => $request->deliveryAddress,
                'delivery_time_slot' => $request->deliveryTimeSlot,
            ]);

            $total = 0;

            // Add order items
            foreach ($request->items as $item) {
                $meal = Meal::findOrFail($item['meal_id']);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $meal->price_cents,
                ]);

                $total += $meal->price_cents * $item['quantity'];
            }

            // Update order total
            $order->update(['total_cents' => $total]);

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load(['user', 'items.meal']))
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get a specific order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function show(Order $order): OrderResource
    {
        $order->load(['user', 'items.meal']);
        return new OrderResource($order);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update an existing order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order = DB::transaction(function () use ($request, $order) {
            $order->update([
                'status' => $request->status ?? $order->status,
                'delivery_address' => $request->delivery_address ?? $order->deliveryAddress,
                'delivery_time_slot' => $request->delivery_time_slot ?? $order->deliveryTimeSlot,
            ]);

            return $order;
        });

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => new OrderResource($order->load(['user', 'items.meal']))
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete an order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Only pending orders can be deleted"
     *     )
     * )
     */
    public function destroy(Order $order): JsonResponse
    {
        // Only allow cancellation of pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be deleted'
            ], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/status-options",
     *     summary="Get order status options",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     )
     * )
     */
    public function statusOptions(): JsonResponse
    {
        return response()->json([
            'data' => [
                'pending',
                'confirmed',
                'preparing',
                'delivered',
                'cancelled'
            ]
        ]);
    }
}
