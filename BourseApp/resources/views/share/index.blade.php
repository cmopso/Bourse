@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive" style="width: 800px">
</p>
                        <table class="table table-striped table-hover display" id="playerList">
                            <thead>
                                <tr>
                                    <th class="text-left" style="width: 300px">Action</th>
                                    <th class="text-left" style="width: 200px">Code ISIN</th>
                                    <th class="text-left" style="width: 200px">Type</th>
                                    <th class="text-left" style="width: 50px">Edit</th>
                                    <th class="text-left" style="width: 50px">Delete</th>
                                </tr>
                            </thead>
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
                                        <td> 
                                            <select class="form-control form-control-sm" name="type"> 
                                                <option value="share">Action</option>
                                                <option value="tracker">Tracker</option>
                                                <option value="fund">Fond commum de placement</option>
                                            </select>
                                        </td>
                                    <td colspan=2> <button type="submit" class="btn btn-primary">Ajouter</button></td>
                                </tr>  
                            @foreach($shares as $share)
                                <tr>
                                    <td>{{ $share->name }}</td>
                                    <td>{{ $share->codeISIN }}</td>
                                    <td>{{ $share->type }}</td>
                                    <td><a href="{{ route('shareEdit', $share) }}"><i class="far fa-edit"></i></a></td>
                                    <td><a href="{{ route('shareDelete', $share) }}"><i class="far fa-trash-alt"></i></a>
                                    </td>
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


