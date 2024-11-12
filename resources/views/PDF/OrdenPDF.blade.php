<!DOCTYPE html>
<html>
<head>
    <title>Reporte PDF</title>
    <style>
        /* Estilos básicos inspirados en Bootstrap */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }
        .table thead th {
            background-color: #f8f9fa;
            text-align: left;
        }
        .py-3 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .overflow-x-auto {
            overflow-x: auto;
        }
        .rounded-md {
            border-radius: 0.375rem;
        }
        .shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="py-3">
        <div class="overflow-x-auto rounded-md shadow">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Plus</th>
                        <th>Producto</th>
                        @foreach ($dataThead as $tienda)
                            <th>{{ $tienda['codigo'] }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $producto)
                    <tr>
                        <td>{{ $producto['plus'] }}</td>
                        <td>{{ $producto['producto'] }}</td>
                        @foreach ($dataThead as $tienda)
                            @if ($tienda['id_tienda'] == 0) 
                                <td></td>
                            @else
                                <td>{{ $producto["pedido_{$tienda['id_tienda']}"] ?? '-' }}</td>
                            @endif
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
