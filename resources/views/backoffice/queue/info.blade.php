@extends('backoffice.layouts.app', [
    'header' => 'Общая информация',
    'breadcrumbs' => ['Очередь'],
])

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Общая информация</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-hover dataTable" role="grid">
                <tbody>
                <tr>
                    <th>Число активных задании</th>
                    <td>{{$queueSize}}</td>
                </tr>
                <tr>
                    <th>Число проваленных заданий</th>
                    <td>{{$errorsCount}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
