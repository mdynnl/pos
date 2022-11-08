<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.min.css">
</head>

<body>
    <h1 align="center">
        XYZ Store
    </h1>
    <h4 align="right" style="margin-right: 2em;">{{ date_format($order->updated_at, 'D, d M Y h:m:s A') }}</h4>
    <table class="pure-table pure-table-horizontal pure-table-striped" align="center">
        <thead>
            <tr align="right">
                <th width="2em">#</th>
                <th align="left" width="12em">Name</th>
                <th width="6em">Price</th>
                <th>Quantity</th>
                <th width="6em">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr align="right">
                    <td>{{ $loop->index + 1 }}</td>
                    <td align="left">{{ $item->name }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>{{ $item->pivot->quantity * $item->price }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top: 1px solid">
                <th align="left" colspan="4">Paid</th>
                <th align="right">{{ $order->paid }}</th>
            </tr>
            <tr>
                <th align="left" colspan="4">Change</th>
                <th align="right">{{ $order->change }}</th>
            </tr>
            <tr>
                <th align="left" colspan="4">Total</th>
                <th align="right">{{ $order->total }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
