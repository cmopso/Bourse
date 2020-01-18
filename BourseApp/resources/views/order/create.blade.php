@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-push-1 col-md-10">
          
            @if (isset($order))
                <form method="POST" action ="/order/{{ $order->id}}">
            @else
                <form method="POST" action ="/order">
            @endif 
            
                @csrf
                @method('PUT')
                </p>
                <p class="text-right"><i class="text-navy">* Champs obligatoires</i></p>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="passedOn">Date execution</label> 
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('passedOn'))
                                <input class="form-control @error('passedOn') is-invalid @enderror" type="text" name="passedOn" id="passedOn" value="{{ old('passedOn') }}" required>
                            @else
                                <input class="form-control @error('passedOn') is-invalid @enderror" type="text" name="passedOn" id="passedOn" value="{{$order->passedOn->format('d/m/Y') }}" required>
                            @endif
                        @else
                            <input class="form-control @error('passedOn') is-invalid @enderror" type="text" name="passedOn" id="passedOn" value="{{ old('passedOn')}}" required>
                        @endif
                        @error('passedOn')
                            <p class="help is-danger">{{ $errors->first('passedOn')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="type">Type</label> 
                        <i class="text-navy">*</i>
                    </div>

                    <div class="col-md-9">
                        <select name="type" id="type">
                            @if (null !== old('type'))
                                <option value="buy" {{ (old("type") == "buy" ? "selected":"") }}> Achat</option>
                                <option value="sale" {{ (old("type") == "sale" ? "selected":"") }}>Vente</option>
                                <option value="dividend" {{ (old("type") == "dividend" ? "selected":"") }}>Dividende</option>
                                <option value="other" {{ (old("type") == "other" ? "selected":"") }}>Autre</option>
                            @else
                                <option value="buy" {{ ($order->type == "buy" ? "selected":"") }}> Achat</option>
                                <option value="sale" {{ ($order->type == "sale" ? "selected":"") }}>Vente</option>
                                <option value="dividend" {{ ($order->type == "dividend" ? "selected":"") }}>Dividende</option>
                                <option value="other" {{ ($order->type == "other" ? "selected":"") }}>Autre</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="share_id">Action</label> 
                        <i class="text-navy">*</i>
                    </div>

                    <div class="col-md-9">
                        <select name="share_id" id="share_id">
                            @if (null !== old('share_id'))
                                @foreach($shares as $share)
                                    <option value="{{$share->id}}" {{ (old("share_id") == $share->id ? "selected":"") }}>{{$share->name}}</option>
                                @endforeach
                            @else
                                @foreach($shares as $share)
                                    <option value="{{$share->id}}" {{ ($order->share_id == $share->id ? "selected":"") }}>{{$share->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="quantity">Quantité</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('quantity'))
                                <input class="form-control @error('quantity') is-invalid @enderror" type="text" name="quantity" id="quantity" value="{{ old('quantity') }}" required>
                            @else
                                <input class="form-control @error('quantity') is-invalid @enderror" type="text" name="quantity" id="quantity" value="{{$order->quantity }}" required>
                            @endif
                        @else
                            <input class="form-control @error('quantity') is-invalid @enderror" type="text" name="quantity" id="quantity" value="{{ old('quantity')}}" required>
                        @endif
                        @error('quantity')
                            <p class="help is-danger">{{ $errors->first('quantity')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="price">Prix</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('price'))
                                <input class="form-control @error('price') is-invalid @enderror" type="text" name="price" id="price" value="{{ old('price') }}" required>
                            @else
                                <input class="form-control @error('price') is-invalid @enderror" type="text" name="price" id="price" value="{{$order->price }}" required>
                            @endif
                        @else
                            <input class="form-control @error('price') is-invalid @enderror" type="text" name="price" id="price" value="{{ old('price')}}" required>
                        @endif
                        @error('price')
                            <p class="help is-danger">{{ $errors->first('price')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="totalPrice">Total brut</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('totalPrice'))
                                <input class="form-control @error('totalPrice') is-invalid @enderror" type="text" name="totalPrice" id="totalPrice" value="{{ old('totalPrice') }}" required>
                            @else
                                <input class="form-control @error('totalPrice') is-invalid @enderror" type="text" name="totalPrice" id="totalPrice" value="{{$order->totalPrice }}" required>
                            @endif
                        @else
                            <input class="form-control @error('totalPrice') is-invalid @enderror" type="text" name="totalPrice" id="totalPrice" value="{{ old('totalPrice')}}" required>
                        @endif
                        @error('totalPrice')
                            <p class="help is-danger">{{ $errors->first('totalPrice')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="totalChargedPrice">Total net</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('totalChargedPrice'))
                                <input class="form-control @error('totalChargedPrice') is-invalid @enderror" type="text" name="totalChargedPrice" id="totalChargedPrice" value="{{ old('totalChargedPrice') }}" required>
                            @else
                                <input class="form-control @error('totalChargedPrice') is-invalid @enderror" type="text" name="totalChargedPrice" id="totalChargedPrice" value="{{$order->totalChargedPrice }}" required>
                            @endif
                        @else
                            <input class="form-control @error('totalChargedPrice') is-invalid @enderror" type="text" name="totalChargedPrice" id="totalChargedPrice" value="{{ old('totalChargedPrice')}}" required>
                        @endif
                        @error('totalChargedPrice')
                            <p class="help is-danger">{{ $errors->first('totalChargedPrice')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="comment">Commentaire</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($order))
                            @if (null !== old('comment'))
                                <input class="form-control @error('comment') is-invalid @enderror" type="text" name="comment" id="comment" value="{{ old('comment') }}">
                            @else
                                <input class="form-control @error('comment') is-invalid @enderror" type="text" name="comment" id="comment" value="{{$order->comment }}">
                            @endif
                        @else
                            <input class="form-control @error('comment') is-invalid @enderror" type="text" name="comment" id="comment" value="{{ old('comment')}}">
                        @endif
                        @error('comment')
                            <p class="help is-danger">{{ $errors->first('comment')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group text-center">
                    <button class="button is-link" type="submit">Mise à jour</button>
                    
                </div>

            </form>

        </div>
    </div>

@stop

