<?php 
    $path = public_path('img/ElHiper.jpg');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporte {{ $fecha }}</title>
    <style>
        /* Generales */
        .container {
            font-family: Arial, Helvetica, sans-serif;
        }

        .border {
            margin-top: 10px;
            margin-bottom: 10px; 
            border-top: 1px solid;
        }

        .gridTable {
            width: 100%;
        }

        /* Encabezado */
        .divLogo {
            width: 40%; 
            text-align: left; 
            padding-left: 20px;
        }

        .imgLogo {
            height: 75px; 
            width: auto;
        }

        .reporteTipo {
            width: 60%; 
            text-align: left; 
            font-size: 16px;
            font-weight: bold;
        }

        /* Información */
        .divTitle {
            width: 12%;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }

        .divData {
            width: 88%;
            text-align: left;
            font-size: 12px;
            padding-left: 10px;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        /* Tabla de Productos */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #acf95e;
            padding: 0.75rem;
            font-size: 12px;
        }

        .table thead th {
            background-color: #acf95e;
            text-align: center;
        }

        .py-3 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
    </style>
</head>
<body class="container">
    <table class="gridTable">
        <tr>
            <td class="divLogo">
                <img src="{{ $img }}" alt="Logo de la Empresa" class="imgLogo">
            </td>
            <td class="reporteTipo">
                Listado de Productos
            </td>
        </tr>
    </table>
    
    <div class="border"></div>
    
    <table class="gridTable">
        <tr>
            <td class="divTitle">Fecha:</td>
            <td class="divData">{{ $fecha }}</td>
        </tr>
        <tr style="margin: 50px;">
            <td class="divTitle">Tienda/s:</td>
            <td class="divData">{{ $nombreTiendas }}</td>
        </tr>
    </table>

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
</body>
</html>
