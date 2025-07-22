<html>
<head>
    <meta charset="UTF-8">
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
            width: 15%;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }

        .divData {
            width: 85%;
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
    @yield('header')
    @yield('content')
</body>
</html>
