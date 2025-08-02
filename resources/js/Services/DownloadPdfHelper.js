import axios from 'axios';

function generarNombreArchivo(nombreTienda, fecha) {
    return `Reporte_${nombreTienda}_${fecha}.pdf`;
}

export async function descargarPdfPedido(orden) {
    try {
        const idPedido = orden.id_pedido;
        const fechaPedido = orden.fecha || orden.fecha_pedido;
        const nombreTienda = orden.tienda || orden.nombre_tienda || orden.nombre;

        const response = await axios.post(`/order/${idPedido}/generatePDF`, {}, {
            responseType: 'blob'
        })

        const nombreArchivo = generarNombreArchivo(nombreTienda, fechaPedido);
        const blob = new Blob([response.data], {type: 'application/pdf'});
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');

        // agregamos los atributos del al link que nos descargara el PDF
        link.href = url;
        link.setAttribute('download', nombreArchivo);
        document.body.appendChild(link);
        link.click();
        link.remove(); // Limpiamos el DOM
        window.URL.revokeObjectURL(url); // Liberar memoria
    } catch (error) {
        console.error("Error al descargar el PDF:", error);
    }
}