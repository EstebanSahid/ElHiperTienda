<?php 
    $path = public_path('img/ElHiper.jpg');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<table class="gridTable">
    <tr>
        <td class="divLogo">
            <img src="{{ $img }}" alt="Logo de la Empresa" class="imgLogo">
        </td>
        <td class="reporteTipo">
            {{ $titulo ?? 'Reporte PDF' }}
        </td>
    </tr>
</table>

<div class="border"></div>

<table class="gridTable">
    <tr>
        <td class="divTitle">Fecha:</td>
        <td class="divData">{{ $fecha }}</td>
    </tr>
    <tr>
        <td class="divTitle">Tienda/s:</td>
        <td class="divData">{{ $nombreTiendas }}</td>
    </tr>
    <tr>
        <td class="divTitle">Orden/es:</td>
        <td class="divData">{{ $numerosOrden }}</td>
    </tr>
    <tr>
        <td class="divTitle">Elaborado Por:</td>
        <td class="divData">{{ $usuarioGenera }}</td>
    </tr>
</table>
