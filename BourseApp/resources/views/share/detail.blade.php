@extends('layouts/app')

@section('content')
    
    <div class="row">
        <div class="col-md-12">
            </p>
            @if(isset($priceShareData['name']))
                <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
                    <a class="btn btn-primary" href="{{ route('shareDetail', $priceShareData['previous']) }}" role="button">< {{$priceShareData['name'][$priceShareData['previous']]}}</a>
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $oneShare->name}}</button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach(['share','indice','fund', 'tracker'] as $type)
                            <a class="dropdown-item" href="#">----- {{$type}} -----</a>
                            @if(isset($shares[$type]))
                                @foreach ($shares[$type] as $key => $share)
                                    <a class="dropdown-item" href="{{ route('shareDetail', $share->id) }}">{{$share->name}}</a>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <a class="btn btn-primary" href="{{ route('shareDetail', $priceShareData['next']) }}" role="button">{{$priceShareData['name'][$priceShareData['next']]}}></a>

                    <label class="col-form-label nav-link active" style="color:#FFFFFF;">From :</label>
                    <input type="text" id="fromfield" class="amcharts-input form-control col-md-1" />
                    <label class="col-form-label nav-link active" style="color:#FFFFFF;">To :</label>
                    <input type="text" id="tofield" class="amcharts-input form-control col-md-1" />
                    
                    <div class="btn-group btn-group-toggle" role="group" data-toggle="buttons">
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="b1m"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">1m</label>
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="b3m"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">3m</label>
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="b6m"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">6m</label>
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="b1y"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">1y</label>
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="bytd"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">YTD</label>
                        <label class="btn btn-primary" class="amcharts-input btn btn-primary" id="bmax"> <input type="radio" class="amcharts-input btn btn-primary" name="options" autocomplete="off">MAX</label>
                    </div>
                </nav>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="margin-left: 0px;">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="col-md-auto">
                            <small>
                                @if(isset($analyze[$oneShare->id]))
                                    <strong>{{ $analyze[$oneShare->id]['totalShare'] }}</strong> actions en portefeuille. </br>
                                    Le prix de revient est de <strong>{{ number_format($analyze[$oneShare->id]['averageCost'], 2, ',', ' ') }} €</strong> </br>
                                    Le cours actuel est de <strong>{{ number_format($lastPrices[$oneShare->id], 2, ',', ' ') }} €</strong> </br>
                                    Le cumul des Gains & pertes est de <strong style={{ ($analyze[$oneShare->id]['totalWinLoss']>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$oneShare->id]['totalWinLoss'], 2, ',', ' ') }} €</strong>.</br>
                                    @if ($analyze[$oneShare->id]['totalShare'])
                                        Les Gains/pertes en cours sont de <strong style={{ (($analyze[$oneShare->id]['totalShare'] * ($lastPrices[$oneShare->id] - $analyze[$oneShare->id]['averageCost']))>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$oneShare->id]['totalShare'] * ($lastPrices[$oneShare->id] - $analyze[$oneShare->id]['averageCost']),2, ',', ' ') }} €</strong></br>
                                    @endif
                                    Dividende de <strong>{{ $oneShare->dividendValue}} €</strong> soit {{$oneShare->yield}} % (moy. {{ $oneShare->fiveYearsAvgDividendYield}} %) versé le {{ $oneShare->dividendDate->day}}/{{ $oneShare->dividendDate->month}}.</br>
                                @endif
                            </small>
                            <div class="table-responsive" >
                                <table class="table table-striped table-hover table-sm display" id="playerList">
                                    <thead>
                                        <tr>
                                            <th class="text-left" >Période</th>
                                            <th class="text-right" >Var %</th>
                                            <th class="text-right" >Valeur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($priceShareData["history"]))
                                            @foreach ($priceShareData["history"]["value"] as $key => $value)
                                                <tr>
                                                    <td class="text-left">{{ $key}}</td>
                                                    @if ($priceShareData["history"]["value"][$key] != "-")
                                                        <td class="text-right"><strong style={{ ($priceShareData["history"]["percent"][$key]>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($priceShareData["history"]["percent"][$key], 2, ',', ' ') }} %</td>
                                                        <td class="text-right">{{ number_format($priceShareData["history"]["value"][$key], 2, ',', ' ') }} €</td>
                                                    @else
                                                        <td class="text-right">NA</td>
                                                        <td class="text-right">NA</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            @if(isset($analyze[$oneShare->id]['totalShare']))
                                                <tr>
                                                    <td class="text-left">Prix revient</td> 
                                                    <td class="text-right"><strong style={{ (($lastPrices[$oneShare->id] -$analyze[$oneShare->id]['averageCost'])/$analyze[$oneShare->id]['averageCost']>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format(100* ($lastPrices[$oneShare->id] -$analyze[$oneShare->id]['averageCost'])/$analyze[$oneShare->id]['averageCost'], 2, ',', ' ') }} %</td>
                                                    <td class="text-right">{{ number_format($analyze[$oneShare->id]['averageCost'], 2, ',', ' ') }} €</td>
                                                </tr>
                                            @endif
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="ibox float-e-margins">
                        <div class="ibox-content">
                        </p>
                        @if(isset($priceShareData["price"]))
                            @include('share.graph')
                        @endif

                        @if($orders->count() != 0)
                        <div class="table-responsive table-sm">
                            </p>
                            <table class="table table-striped table-hover display" id="playerList">
                                <thead>
                                    <tr>
                                        <th class="text-left" >Date</th>
                                        <th class="text-left" >Ordre</th>
                                        <th class="text-right" >Quantité</th>
                                        <th class="text-right" >Prix</th>
                                        <th class="text-right" >Gain/Perte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="text-left">{{ $order->passedOn->format("d/m/Y") }}</td>
                                            <td class="text-left">{{ $order->type}}</td>
                                            <td class="text-right">{{ number_format($order->quantity, 2, ',', ' ') }}</td>
                                            <td class="text-right">{{ number_format($order->price, 2, ',', ' ') }} €</td>
                                            @if(isset($analyze[$oneShare->id]['winLoss'][$order->id]))
                                            <td class="text-right"><strong style={{ ($analyze[$oneShare->id]['winLoss'][$order->id]>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$oneShare->id]['winLoss'][$order->id], 2, ',', ' ') }} €</td>
                                            @else 
                                            <td class="text-right"> - </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            pas d'ordre passé pour  cette action
                        @endif
                    </div>
                </div>
        </div>
    </div>
@stop


