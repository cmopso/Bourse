@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive" style="width: 1800px">
                        </p>
                        <table class="table table-striped table-hover display" id="orderList">
                            <thead>
                                <tr>
                                    <th class="text-left" style="width: 150px">Date</th>
                                    <th class="text-left" style="width: 150px">Ordre</th>
                                    <th class="text-left" style="width: 200px">Action</th>
                                    <th class="text-left" style="width: 100px"> Quantité</th>
                                    <th class="text-left" style="width: 100px"> Prix</th>
                                    <th class="text-left" style="width: 100px">Total Brut</th>
                                    <th class="text-left" style="width: 100px">Total Net</th>
                                    <th class="text-left" style="width: 600px">Commentaire</th>
                                    <th class="text-left" style="width: 100px">Frais</th>
                                    <th class="text-left" style="width: 100px">% Frais</th>
                                    <th class="text-left" style="width: 50px">Edit</th>
                                    <th class="text-left" style="width: 50px">Delete</th>
                                </tr>
                            </thead>
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
                                                <option value="buy">Achat</option>
                                                <option value="sale">Vente</option>
                                                <option value="dividen">Dividende</option>
                                                <option value="other">Autre</option>
                                            </select>
                                        </td>
                                        <td> 
                                            <select class="form-control form-control-sm" name="share_id">
                                                @foreach($shares as $share)
                                                    <option value="{{$share->id}}">{{$share->name}}</option>
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
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->passedOn->format("d/m/Y") }}</td>
                                    <td>{{ $order->type }}</td>
                                    <td>{{ $order->share->name }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ number_format($order->price, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->totalPrice, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->totalChargedPrice, 2, ',', ' ') }} €</td>
                                    <td>{{ $order->comment }}</td>
                                    <td>{{ number_format($order->charges, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format($order->chargesPercent * 100, 2, ',', ' ') }} %</td>
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


