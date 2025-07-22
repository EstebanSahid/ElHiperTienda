@extends('pdf.styles.baseVerde')

@section('header')
    @include('pdf.partials.HeaderPDF', [
        'fecha' => $fecha,
        'nombreTiendas' => $nombreTiendas,
        'numerosOrden' => $numerosOrden,
        'usuarioGenera' => $usuarioGenera,
        'titulo' => 'Listado de Productos'
    ])
@endsection

@section('content')
    <div class="border"></div>

    <div class="py-3">
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
@endsection
