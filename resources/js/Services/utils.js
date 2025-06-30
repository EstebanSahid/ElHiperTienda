export function FocoUltimoInputConTransicion(refs) {
    if (refs && refs.length > 0) {
        const ultimoInput = refs[refs.length - 1];
        ultimoInput.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' }
        );
        setTimeout(() => {
            ultimoInput.focus();
        }, 300);
    }
}
