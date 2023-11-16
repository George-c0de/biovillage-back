@extends('mail.layouts.text')

@section('content')
Номер заказа: {{ $order['id'] }}
Ссылка на заказ: {{ env('APP_URL') }}/admin/orders/{{ $order['id'] }}/
Дата заказа: {{$order['createdAt']}}
Клиент: {{ $order['clientPhone'] }} {{ $order['clientName'] }}
Доставка: {{$order['deliveryDate']}} c {{$order['diStartHour']}}:{{$order['diStartMinute']}} по {{$order['diEndHour']}}:{{$order['diEndMinute']}}
Сумма заказа: {{ $order['total'] }}
@endsection
