@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        </p>
                        <div class="col-md-1">

                            <div class="btn-group" role="group" aria-label="toolbar">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $oneShare->name}}
                                    </button>
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
                                </div>
                            </div>
                            <!--
                            <select class="form-control form-control-sl" name="shareid" onChange="location = this.options[this.selectedIndex].value;">> 
                                @foreach(['share','indice','fund', 'tracker'] as $type)
                                    <option value="">----- {{$type}} -----</option>
                                    @if(isset($shares[$type]))
                                        @foreach ($shares[$type] as $share)
                                        <option value="{{ route('shareDetail', $share->id) }}" {{ ($share->id == $oneShare->id ? "selected":"") }}>{{$share->name}}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                            -->
                        </div>
                        <small>
                            @if(isset($analyze[$oneShare->id]))
                                <strong>{{ $analyze[$oneShare->id]['totalShare'] }}</strong> actions en portefeuille. 
                                Le prix de revient est de <strong>{{ number_format($analyze[$oneShare->id]['averageCost'], 2, ',', ' ') }} €</strong> </br>
                                Le cumul des Gains & pertes est de <strong style={{ ($analyze[$oneShare->id]['totalWinLoss']>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$oneShare->id]['totalWinLoss'], 2, ',', ' ') }} €</strong>.
                                @if ($analyze[$oneShare->id]['totalShare'])
                                    Les Gains/pertes en cours sont de <strong style={{ (($analyze[$oneShare->id]['totalShare'] * ($lastPrices[$oneShare->id] - $analyze[$oneShare->id]['averageCost']))>0?"color:#00AA00":"color:#FF0000")}}>{{ number_format($analyze[$oneShare->id]['totalShare'] * ($lastPrices[$oneShare->id] - $analyze[$oneShare->id]['averageCost']),2, ',', ' ') }} €</strong>
                                @endif
                            @endif
                        </small>

                        @include('share.graph')

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


