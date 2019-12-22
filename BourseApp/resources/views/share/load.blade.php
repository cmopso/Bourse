@extends('layouts/app')

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive" style="width: 1200px">
                        </p>
                        <table class="table table-striped table-hover display" id="playerList">
                            <thead>
                                <tr>
                                    <th class="text-left" style="width: 200px">Action</th>
                                    <th class="text-left" style="width: 100px">Code ISIN</th>
                                    <th class="text-left" style="width: 150px">Du</th>
                                    <th class="text-left" style="width: 150px">Au</th>
                                    <th class="text-left" style="width: 150px">Ligne importée</th>
                                    <th class="text-left" style="width: 150px">Erreurs</th>
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


