@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive">
                        </p>
                        <table class="table table-striped table-hover  display" id="orderList">
                            <tbody>
                                <tr>
                                    <form method="POST" action ="/order">
                                        @csrf
                                        @method('PUT')
                                        <td> <input type="text" class="form-control @error('passedOn') is-invalid @enderror" name="passedOn" value="{{ old('passedOn')}}" required placeholder="date execution">
                                            @error('passedOn') <p class="help is-danger">{{ $errors->first('passedOn')}}</p> @enderror
                                        </td>
                                        <td> 
                                            <select class="form-control form-control-sm" name="type">  
                                                <option value="buy" {{ (old("type") == "buy" ? "selected":"") }}> Achat</option>
                                                <option value="sale" {{ (old("type") == "sale" ? "selected":"") }}>Vente</option>
                                                <option value="dividend" {{ (old("type") == "dividend" ? "selected":"") }}>Dividende</option>
                                                <option value="other" {{ (old("type") == "other" ? "selected":"") }}>Autre</option>
                                            </select>
                                        </td>
                                        <td> 
                                            <select class="form-control form-control-sm" name="share_id">
                                                @foreach($shares as $share)
                                                    <option value="{{$share->id}}" {{ (old("share_id") == $share->id ? "selected":"") }}>{{$share->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td> <input type="text" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity')}}" required placeholder="quantité">
                                            @error('quantity') <p class="help is-danger">{{ $errors->first('quantity')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price')}}" required placeholder="prix">
                                            @error('price') <p class="help is-danger">{{ $errors->first('price')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('totalPrice') is-invalid @enderror" name="totalPrice" value="{{ old('totalPrice')}}" required placeholder="total brut">
                                            @error('totalPrice') <p class="help is-danger">{{ $errors->first('totalPrice')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('totalChargedPrice') is-invalid @enderror" name="totalChargedPrice" value="{{ old('totalChargedPrice')}}" required placeholder="total net">
                                            @error('totalChargedPrice') <p class="help is-danger">{{ $errors->first('totalChargedPrice')}}</p> @enderror
                                        </td>
                                        <td> <input type="text" class="form-control @error('comment') is-invalid @enderror" name="comment" value="{{ old('comment')}}" placeholder="commentaire">
                                            @error('comment') <p class="help is-danger">{{ $errors->first('comment')}}</p> @enderror
                                        </td>
                                    <td colspan=4> <button type="submit" class="btn btn-primary">Ajouter</button></td>
                                </tr>  
                            </tbody>
                        </table>
                        <table class="table table-striped table-hover table-sm display" id="orderList">
                            <thead>
                                <tr>
                                    <th class="text-left" >Date</th>
                                    <th class="text-left" >Ordre</th>
                                    <th class="text-left" >Action</th>
                                    <th class="text-left" > Quantité</th>
                                    <th class="text-left" > Prix</th>
                                    <th class="text-left" >Total Brut</th>
                                    <th class="text-left" >Total Net</th>
                                    <th class="text-left" >Frais</th>
                                    <th class="text-left" >% Frais</th>
                                    <th class="text-left" >Commentaire</th>
                                    <th class="text-left" >Edit</th>
                                    <th class="text-left" >Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->passedOn->format("d/m/Y") }}</td>
                                    <td>{{ $order->type }}</td>
                                    <td><a href="{{ route('shareDetail', $order->share->id) }}">{{ $order->share->name }}</a></td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ number_format($order->price, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->totalPrice, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->totalChargedPrice, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->charges, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->chargesPercent, 2, ',', ' ') }} %</td>
                                    <td>{{ $order->comment }}</td>
                                    <td><a href="{{ route('orderEdit', $order) }}"><i class="far fa-edit"></i></a></td>
                                    <td><a href="{{ route('orderDelete', $order) }}"><i class="far fa-trash-alt"></i></a>         
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


