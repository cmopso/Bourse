@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-auto">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive overflow-hidden">
                        </p>
                        <table class="table table-striped table-hover display">
                            <tbody>
                                <tr>
                                    <form method="POST" action ="/share">
                                        @csrf
                                        @method('PUT')
                                        <td> <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name')}}" required placeholder="nom action">
                                            @error('name') <p class="help is-danger">{{ $errors->first('name')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('codeISIN') is-invalid @enderror" name="codeISIN" value="{{ old('codeISIN')}}" required placeholder="code ISIN">
                                            @error('codeISIN') <p class="help is-danger">{{ $errors->first('codeISIN')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code')}}" required placeholder="code">
                                            @error('code') <p class="help is-danger">{{ $errors->first('code')}}</p> @enderror
                                        </td>
                                        <td> 
                                            <select class="form-control form-control-sm" name="type"> 
                                                <option value="share">Action</option>
                                                <option value="option">Option</option>
                                                <option value="tracker">Tracker</option>
                                                <option value="fund">FCP</option>
                                                <option value="indice">Indice</option>
                                            </select>
                                        </td>
                                    <td colspan=1> <button type="submit" class="btn btn-primary">Ajouter</button></td>
                                </tr> 
                            </tbody>
                        </table>

                        <table class="table table-striped table-hover table-sm display" id="playerList">
                            <thead>
                                <tr>
                                    <th class="text-left" >Action</th>
                                    <th class="text-left" >Code ISIN</th>
                                    <th class="text-left" >Code</th>
                                    <th class="text-right" >Quantité</th>
                                    <th class="text-right" >Gain/Perte</th>
                                    <th class="text-right" >Prix Revient</th>
                                    <th class="text-right" >Cours</th>
                                    <th class="text-right" >Bénéfice</th>
                                    <th class="text-center" >%</th>
                                    <th class="text-center" >Valeur</th>
                                    <th class="text-right" >Mise à jour</th>
                                    <th class="text-center" ></th>
                                    <th class="text-center" ></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['share', 'option', 'indice','fund', 'tracker'] as $type)
                                    @if(isset($shares[$type]))
                                        <tr>
                                            <th colspan=4 class="text-right alert alert-primary"></th>
                                            <th colspan=1 class="text-right alert alert-primary">{{number_format($summary[$type]['totalWinLoss'], 2, ',', ' ')}} €</th>
                                            <th colspan=2 class="text-right alert alert-primary"></th>
                                            <th colspan=1 class="text-right alert alert-primary">{{number_format($summary[$type]['totalBenefit'], 2, ',', ' ')}} €</th>
                                            <th colspan=1 class="text-right alert alert-primary">{{number_format($summary[$type]['totalBenefitPercent']*100, 2, ',', ' ')}} %</th>
                                            <th colspan=1 class="text-right alert alert-primary">{{number_format($summary[$type]['totalValue'], 2, ',', ' ') }} €</th>
                                            <th colspan=3 class="text-right alert alert-primary">{{$type}}</th>
                                        </tr>
                                        @foreach($shares[$type] as $share)
                                            <tr>
                                                <td class="text-left"><a href="{{ route('shareDetail', $share->id) }}">{{ $share->name }}</a></td>
                                                <td class="text-left">{{$share->codeISIN}}</td>
                                                <td class="text-left">{{$share->code}}</td>
                                                @if(isset($analyze[$share->id]))
                                                    @if($analyze[$share->id]['totalShare'])
                                                        <td class="text-right">{{ number_format($analyze[$share->id]['totalShare'], 2, ',', ' ') }}</td>
                                                    @else
                                                        <td class="text-right">-</td>
                                                    @endif
                                                    @if($analyze[$share->id]['totalWinLoss'])
                                                        <td class="text-right"><strong style={{ ($analyze[$share->id]['totalWinLoss']>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$share->id]['totalWinLoss'], 2, ',', ' ') }} €</td>
                                                    @else
                                                        <td class="text-right">-</td>
                                                    @endif
                                                    <td class="text-right">{{ number_format($analyze[$share->id]['averageCost'], 2, ',', ' ') }} €</td>
                                                    <td class="text-right">{{ number_format($lastPrices["value"][$share->id], 2, ',', ' ') }} €</td>
                                                    @if($analyze[$share->id]['totalShare'])
                                                        <td class="text-right"><strong style={{ ($analyze[$share->id]['averageCost']<$lastPrices["value"][$share->id]?"color:#00AA00":"color:#FF0000")}}>{{ number_format(($lastPrices["value"][$share->id]- $analyze[$share->id]['averageCost'])*$analyze[$share->id]['totalShare'], 2, ',', ' ') }} €</td>
                                                        <td class="text-right"><strong style={{ ($analyze[$share->id]['averageCost']<$lastPrices["value"][$share->id]?"color:#00AA00":"color:#FF0000")}}>{{ number_format(($lastPrices["value"][$share->id]- $analyze[$share->id]['averageCost'])/$analyze[$share->id]['averageCost'] * 100, 2, ',', ' ') }} %</td>
                                                        <td class="text-right"><strong>{{ number_format($lastPrices["value"][$share->id] * $analyze[$share->id]['totalShare'], 2, ',', ' ') }} €</strong></td>
                                                    @else
                                                        <td class="text-right">-</td>
                                                        <td class="text-right">-</td>
                                                        <td class="text-right">-</td>
                                                    @endif
                                                @else
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">{{ number_format($lastPrices["value"][$share->id], 2, ',', ' ') }} €</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">-</td>
                                                @endif
                                                <td class="text-center">{{$lastPrices["message"][$share->id]}}</td>
                                                <td class="text-center"><a href="{{ route('shareEdit', $share) }}"><i class="far fa-edit"></i></a></td>
                                                <td class="text-center"><a href="{{ route('shareDelete', $share) }}"><i class="far fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!--
        @if(isset($oneShare))
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        </p>
                        <h1>{{ $oneShare->name }}</h1>
                        <small>
                            {{ $analyze[$oneShare->id]['totalShare'] }} actions en portefeuille. </br>
                            Le prix de revient est de {{ number_format($analyze[$oneShare->id]['averageCost'], 2, ',', ' ') }} € </br>
                            Le cumul des Gains & pertes est de {{ number_format($analyze[$oneShare->id]['totalWinLoss'], 2, ',', ' ') }} € </br>
                            @if ($analyze[$oneShare->id]['totalShare'])
                                Les Gains/pertes en cours sont de {{ number_format($analyze[$oneShare->id]['totalShare'] * ($lastPrices[$oneShare->id] - $analyze[$oneShare->id]['averageCost']),2, ',', ' ') }} €
                            @endif
                        </small>
                        @if($orders->count() != 0)
                        <div class="table-responsive" style="width: 700px">
                            </p>
                            <table class="table table-striped table-hover display" id="playerList">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width: 150px">Date</th>
                                        <th class="text-left" style="width: 150px">Ordre</th>
                                        <th class="text-right" style="width: 100px">Quantité</th>
                                        <th class="text-right" style="width: 150px">Prix</th>
                                        <th class="text-right" style="width: 150px">Gain/Perte</th>
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
                                            <td class="text-right">{{ number_format($analyze[$oneShare->id]['winLoss'][$order->id], 2, ',', ' ') }} €</td>
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
        @endif
    </div>
    -->
@stop


