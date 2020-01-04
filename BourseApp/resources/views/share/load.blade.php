@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive table-sm" >
                        </p>
                        <table class="table table-striped table-hover display" id="playerList">
                            <thead>
                                <tr>
                                    <th class="text-left" >Action</th>
                                    <th class="text-left" >Code ISIN</th>
                                    <th class="text-left" >Du</th>
                                    <th class="text-left" >Au</th>
                                    <th class="text-left" >Ligne importée</th>
                                    <th class="text-left" >Erreurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $id => $line)
                                    <tr>
                                        <td class="text-left"><a href="{{ route('shareDetail', $id) }}">{{ $line['name'] }}</a></td>
                                        <td class="text-left">{{ $line['codeISIN'] }}</td>
                                        <td class="text-left">{{ $line['start'] }}</td>
                                        <td class="text-left">{{ $line['end'] }}</td>
                                        <td class="text-left">{{ $line['imported'] }}</td>
                                        <td class="text-left">{{ $line['error'] }}</td>
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


