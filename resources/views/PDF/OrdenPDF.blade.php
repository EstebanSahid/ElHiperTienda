<!DOCTYPE html>
<html>
<head>
    <title>Laravel 11 Generate PDF Example - ItSolutionStuff.com</title>
</head>
<body>
    <div class="py-3">
        <div class="overflow-x-auto rounded-md shadow">
            <table>
                <thead>
                    <tr>
                        <th>Plus</th>
                        <th>Producto</th>
                        @foreach ($tiendas as $tienda)
                            <th> {{ $tienda['codigo'] }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $producto)
                    <tr>
                        <td>{{ $producto['plus'] }}</td>
                        <td>{{ $producto['producto'] }}</td>
                        @foreach ($tiendas as $tienda)
                        <td>{{ $producto["pedido_{$tienda['id_tienda']}"] ?? '-' }}</td>
                        @endforeach
                        <td>{{ $producto['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
