@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-push-1 col-md-10">
          
            @if (isset($share))
                <form method="POST" action ="/share/{{ $share->id}}">
            @else
                <form method="POST" action ="/share">
            @endif 
            
                @csrf
                @method('PUT')

                <p class="text-right"><i class="text-navy">* Champs obligatoires</i></p>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="name">Action</label> 
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($share))
                            @if (null !== old('name'))
                                <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name') }}" required>
                            @else
                                <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{$share->name }}" required>
                            @endif
                        @else
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name')}}" required>
                        @endif
                        @error('name')
                            <p class="help is-danger">{{ $errors->first('name')}}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-3">
                        <label class="label" for="codeISIN">code ISIN</label>
                        <i class="text-navy">*</i>
                    </div>
                    <div class="col-md-9">
                        @if (isset($share))
                            @if (null !== old('codeISIN'))
                                <input class="form-control @error('codeISIN') is-invalid @enderror" type="text" name="codeISIN" id="codeISIN" value="{{ old('codeISIN') }}" required>
                            @else
                                <input class="form-control @error('codeISIN') is-invalid @enderror" type="text" name="codeISIN" id="codeISIN" value="{{$share->codeISIN }}" required>
                            @endif
                        @else
                            <input class="form-control @error('codeISIN') is-invalid @enderror" type="text" name="codeISIN" id="codeISIN" value="{{ old('codeISIN')}}" required>
                        @endif
                        @error('codeISIN')
                            <p class="help is-danger">{{ $errors->first('codeISIN')}}</p>
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
                            <option value="share">Action</option>
                            <option value="tracker">Tracker</option>
                            <option value="fund">Fond commum de placement</option>
                        </select>
                    </div>
                </div>

                <div class="form-group text-center">
                    <button class="button is-link" type="submit">Créer</button>
                    
                </div>

            </form>

        </div>
    </div>

@stop

