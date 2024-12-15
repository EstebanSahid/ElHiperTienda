const timeZone = process.env.APP_TIMEZONE;

export const obtenerFechaActual = () => {
    const fechaActual = new Date();
    const opciones = { timeZone, year: 'numeric', month: '2-digit', day: '2-digit' };
    const [dia, mes, anio] = new Intl.DateTimeFormat('es-ES', opciones).format(fechaActual).split('/');
    return `${dia}/${mes}/${anio}`
}

export const obtenerFechaActualGuardarBD = () => {
    const fechaActual = new Date();
    const opciones = { timeZone, year: 'numeric', month: '2-digit', day: '2-digit' };
    const [dia, mes, anio] = new Intl.DateTimeFormat('es-ES', opciones).format(fechaActual).split('/');
    return `${anio}-${mes}-${dia}`
}