/* 
Se captura la zona horaria presente en el archivo .env y
damos las opciones de fecha
*/
const timeZone = process.env.APP_TIMEZONE;
const opciones = { timeZone, year: 'numeric', month: '2-digit', day: '2-digit' };

const formatearFecha = (formatoSalida) => {
    const fechaActual = new Date();
    const [dia, mes, anio] = new Intl.DateTimeFormat('es-ES', opciones).format(fechaActual).split('/');
    
    if (formatoSalida === 'dd/mm/yyyy') {
        return `${dia}/${mes}/${anio}`;
    } else if (formatoSalida === 'yyyy-mm-dd') {
        return `${anio}-${mes}-${dia}`;
    }
    throw new Error(`Formato de salida no soportado: ${formatoSalida}`);
};

export const obtenerFechaActual = () => formatearFecha('dd/mm/yyyy');

export const obtenerFechaActualGuardarBD = () => formatearFecha('yyyy-mm-dd');
