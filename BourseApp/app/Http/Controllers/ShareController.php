<?php

namespace App\Http\Controllers;

use App\Share;
use App\Order;
use App\PriceShares;
use App\Http\Controllers\PriceSharesController;
use App\Http\Controllers\OrderController;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

class ShareController extends Controller
{
    
    public function index(Share $share)
    {
        $shares = Share::all();
        $shares = $this->prepareDisplay($shares);
        $analyze = OrderController::analyzeAllOrders();
        $lastPrices = $this->getLastPriceAllShare();
        
        $summary = [];
        foreach ($shares as $type => $sharesType) {
            $summary[$type]['totalWinLoss'] = 0;
            $summary[$type]['totalBenefit'] = 0;
            $summary[$type]['totalValue'] = 0;
            $summary[$type]['totalInvested'] = 0;
            $summary[$type]['totalBenefitPercent'] = 0;
            foreach ($sharesType as $share) {
                if (isset($analyze[$share->id]['totalWinLoss']))
                    $summary[$type]['totalWinLoss'] = $summary[$type]['totalWinLoss'] + $analyze[$share->id]['totalWinLoss'];
                if (isset($analyze[$share->id]['totalShare'])) {
                    $summary[$type]['totalBenefit'] = $summary[$type]['totalBenefit'] + ($lastPrices["value"][$share->id]- $analyze[$share->id]['averageCost'])*$analyze[$share->id]['totalShare'];
                    $summary[$type]['totalValue'] = $summary[$type]['totalValue'] + $lastPrices["value"][$share->id] * $analyze[$share->id]['totalShare'];
                    $summary[$type]['totalInvested'] = $summary[$type]['totalInvested'] + $analyze[$share->id]['totalShare'] * $analyze[$share->id]['averageCost'];
                }
            }
            if ($summary[$type]['totalInvested'] != 0)
                $summary[$type]['totalBenefitPercent'] = $summary[$type]['totalBenefit'] / $summary[$type]['totalInvested'];
        }
        //dd($summary);
        //dd($analyze);
        return view('share.index', compact('shares', 'analyze', 'lastPrices', 'summary'));
    }

    public function detail(Share $share)
    {
        
        $oneShare = $share;
        $shares = Share::all()->groupBy("type")->sortBy('name');

        $orders = $oneShare->orders;
        $analyze = OrderController::analyzeAllOrders();
        $lastPrices = $this->getLastPriceAllShare();

        $orders = OrderController::prepareDisplay($orders);

        $priceShareData = PriceSharesController::getAllPrices($oneShare);
        
        return view('share.detail', compact('shares', 'oneShare', 'orders', 'analyze', 'lastPrices', 'priceShareData'));
    }

    
    public function create()
    {
        return view('share.create');
    }

    
    public function store(Request $request)
    {
        $share = Share::create($this->validateNewShare());
        return redirect()->route('shareIndex')->with('success', 'action créée');
    }

    
    public function show(Share $share)
    {
        return view('share.show', compact('share'));
    }

    
    public function edit(Share $share)
    {
        return view('share.create', compact('share'));
    }

    
    public function update(Request $request, Share $share)
    {
        $share->update($this->validateUpdateShare($share));
        return redirect()->route('shareIndex')->with('success', 'action mise à jour');
    }

    
    public function destroy(Share $share)
    {
        $share->delete();
        return redirect()->route('shareIndex')->with('success', 'action supprimée');
    }

    public function validateNewShare()
    {
        $validatedAttributes = request()->validate([
            'name' => 'required|unique:shares,name',
            'codeISIN' => 'required|unique:shares,codeISIN',
            'type' => 'required',
            'code' => 'required',
        ]);

        return $validatedAttributes;
    }

    public function validateUpdateShare(Share $share)
    {
        $validatedAttributes = request()->validate([
            'name' => ['required', Rule::unique('shares')->ignore($share->id),],
            'codeISIN' => ['required', Rule::unique('shares')->ignore($share->id),],
            'type' => 'required',
            'code' => 'required',
        ]);

        return $validatedAttributes;
    }

    public function prepareDisplay(Collection $shares)
    {
        $shares = $shares->groupBy('type');
        foreach(['share', 'option', 'indice', 'fund', 'tracker'] as $type) {
            if (isset($shares[$type])) {
                $shares[$type]->sortBy(function($share)
                    {
                        switch ($share->type)
                        {
                            case 'share':
                                return 1;
                            case 'option':
                                return 2;
                            case 'indice':
                                return 3;
                            case 'fund':
                                return 4;
                            case 'tracker':
                                return 5;
                        }
                    }
                );
            }
        }
        foreach($shares as $shareType) {
            foreach($shareType as $share) {
                if ($share->type == "share") $share->type = "Action";
                if ($share->type == "option") $share->type = "Option";
                if ($share->type == "tracker") $share->type = "Tracker";
                if ($share->type == "fund") $share->type = "FCP";
                if ($share->type == "indice") $share->type = "Indice";
            } 
        }
        return $shares;
    }

    public function getLastPriceAllShare()
    {
        $lastPrices = [];
        //$today = Carbon::today();
        $shares = Share::all();
        
        foreach ($shares as $share) {
            $sharePrice = PriceShares::where('share_id', $share->id)->orderBy('date', 'desc')->first();
            if ($sharePrice) {
                $lastPrices["value"][$share->id] = $sharePrice->close;
                $lastPrices["date"][$share->id] = $sharePrice->date->format("d/m/Y");  
                if (Carbon::today()->isWeekend()) {
                    //dd($sharePrice->date->format("d/m/Y") . " " . Carbon::today()->previousWeekday()->format("d/m/Y"));
                    if ($sharePrice->date->format("d/m/Y") == Carbon::today()->previousWeekday()->format("d/m/Y"))
                        $lastPrices["message"][$share->id] = "OK";
                    else
                        $lastPrices["message"][$share->id] = $sharePrice->date->format("d/m/Y");
                } else {
                    if ($sharePrice->date->format("d/m/Y") == Carbon::today()->previousWeekday()->format("d/m/Y"))
                        $lastPrices["message"][$share->id] = "OK";
                    else
                        $lastPrices["message"][$share->id] = $sharePrice->date->format("d/m/Y");
                }
            } else {
                $lastPrices["value"][$share->id] = 0;
                $lastPrices["date"][$share->id] = "";
                $lastPrices["message"][$share->id] = "Pas de donnée";
            }
        }

        return $lastPrices;
    }
}
