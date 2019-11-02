<?php

namespace App\Http\Controllers;

use App\Order;
use App\Share;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    
    public function index()
    {
        $orders = Order::all()->sortByDesc('passedOn');
        $shares = Share::all()->sortBy('name');
        foreach($orders as $order) {
            if ($order->type == "buy") $order->type = "Achat";
            if ($order->type == "sale") $order->type = "Vente";
            if ($order->type == "dividend") $order->type = "Dividende";
            if ($order->type == "other") $order->type = "Autre";
        }
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
        return view('order.edit', compact('order'));
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
            'passedOn'              => 'required|date',
            'type'              => ['required', Rule::in(['buy','sale','dividend','other'])],
            'quantity'          => 'required|numeric',
            'price'             => 'required|numeric',
            'totalPrice'        => 'required|numeric',
            'totalChargedPrice' => 'required|numeric',
            'comment'           => 'nullable',
        ]);
        $validatedAttributes['passedOn'] = date_create($validatedAttributes['passedOn']);
        $validatedAttributes['charges'] = abs($validatedAttributes['totalPrice'] - $validatedAttributes['totalChargedPrice']);
        $validatedAttributes['chargesPercent'] = $validatedAttributes['charges'] / $validatedAttributes['totalPrice'];
        return $validatedAttributes;
    }

    public function validateUpdateOrder(Order $order)
    {
        $validatedAttributes = request()->validate([
            'passedOn'          => 'required',
            'type'              => 'required', 
            'price'             => 'required',
            'quantity'          => 'required',
            'totalPrice'        => 'required',
            'totalchargedPrice' => 'required',
            'charges'           => 'required',
            'chargesPercent'   => 'required',
            'comment'           => 'required',
        ]);

        return $validatedAttributes;
    }
}
