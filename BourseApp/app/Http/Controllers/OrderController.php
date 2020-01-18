<?php

namespace App\Http\Controllers;

use App\Order;
use App\Share;
use App\Http\Controllers\PriceShareController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class OrderController extends Controller
{
    
    public function index()
    {
        $orders = Order::all();
        $shares = Share::all()->sortBy('name');
        $orders = $this->prepareDisplay($orders);
        return view('order.index', compact('orders', 'shares'));
    }

    
    public function create()
    {
         return view('order.create');
    }

    public function store(Request $request)
    {
        $order = Order::create($this->validateNewOrder());
        return redirect()->route('orderIndex')->with('success', 'ordre créé');
    }


    public function show(Order $order)
    {
        return view('order.show', compact('order'));
    }

    
    public function edit(Order $order)
    {
        $shares = Share::all()->sortBy('name');
        return view('order.create', compact('order', 'shares'));
    }

    
    public function update(Request $request, Order $order)
    {
        $order->update($this->validateUpdateOrder($order));
        return redirect()->route('orderIndex')->with('success', 'ordre mis à jour');
    }

    
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orderIndex')->with('success', 'ordre supprimé');
        
    }

    public function validateNewOrder()
    {
        $validatedAttributes = request()->validate([
            'share_id'          => 'required',
            'passedOn'              => 'required|date_format:d/m/Y',
            'type'              => ['required', Rule::in(['buy','sale','dividend','other'])],
            'quantity'          => 'required|numeric',
            'price'             => 'required|numeric',
            'totalPrice'        => 'required|numeric',
            'totalChargedPrice' => 'required|numeric',
            'comment'           => 'nullable',
        ]);
        //dd($validatedAttributes['passedOn']);
        $validatedAttributes['passedOn'] = Carbon::createFromFormat("d/m/Y", $validatedAttributes['passedOn']);
        $validatedAttributes['charges'] = abs($validatedAttributes['totalPrice'] - $validatedAttributes['totalChargedPrice']);
        $validatedAttributes['chargesPercent'] = $validatedAttributes['charges'] / $validatedAttributes['totalPrice'] * 100;
        return $validatedAttributes;
    }

    public function validateUpdateOrder(Order $order)
    {
        $validatedAttributes = request()->validate([
            'share_id'          => 'required',
            'passedOn'          => 'required|date_format:d/m/Y',
            'type'              => ['required', Rule::in(['buy','sale','dividend','other'])],
            'quantity'          => 'required|numeric',
            'price'             => 'required|numeric',
            'totalPrice'        => 'required|numeric',
            'totalChargedPrice' => 'required|numeric',
            'comment'           => 'nullable',
        ]);
        $validatedAttributes['passedOn'] = Carbon::createFromFormat("d/m/Y", $validatedAttributes['passedOn']);
        $validatedAttributes['charges'] = abs($validatedAttributes['totalPrice'] - $validatedAttributes['totalChargedPrice']);
        $validatedAttributes['chargesPercent'] = $validatedAttributes['charges'] / $validatedAttributes['totalPrice'] * 100;
        return $validatedAttributes;
    }

    public static function prepareDisplay(Collection $orders)
    {
        $orders = $orders->sortByDesc('passedOn');
        foreach($orders as $order) {
            if ($order->type == "buy") $order->type = "Achat";
            if ($order->type == "sale") $order->type = "Vente";
            if ($order->type == "dividend") $order->type = "Dividende";
            if ($order->type == "other") $order->type = "Autre";
        } 
        return $orders;
    }

    public static function analyzeShareOrders(Collection $orders)
    {
        $analyze = [];
        $totalShare = 0;
        $averageCost = 0;
        $totalWinLoss =0;
        $winLoss = [];
        $orders->sortBy('passedOn');

        foreach ($orders as $order) {
            switch ($order->type) {
                case 'buy':
                    $averageCost = ($order->quantity * $order->price + $totalShare * $averageCost) / ( $totalShare + $order->quantity);
                    $totalShare += $order->quantity;
                    break;
                case 'sale':
                    $winLoss[$order->id] = ($order->price - $averageCost) * $order->quantity; 
                    $totalWinLoss += $winLoss[$order->id];
                    $totalShare -= $order->quantity;
                    break;
                case 'dividend':
                    if ($order->quantity != 0) {
                        $averageCost = ($order->quantity * $order->price + $totalShare * $averageCost) / ( $totalShare + $order->quantity);
                        $totalShare += $order->quantity;
                    }
                    $winLoss[$order->id] = $order->totalPrice;
                    $totalWinLoss += $winLoss[$order->id];
                    break;
                case 'other':

                    break;
            }
        }
       
        $analyze['totalShare'] = $totalShare;
        $analyze['averageCost'] = $averageCost;
        $analyze['winLoss'] = $winLoss;
        $analyze['totalWinLoss'] = $totalWinLoss;
        return $analyze;
    }

    public static function analyzeAllOrders()
    {
        $allOrders = Order::all()->groupBy('share_id');
        $analyze = [];

        //dd($allOrders);
        foreach ($allOrders as $shareID => $ordersPerShare) {
            //dd($ordersPerShare);
            $analyze[$shareID] = OrderController::analyzeShareOrders($ordersPerShare);
        }
         return $analyze;
    }
}
