import { ref, computed } from 'vue';

export function useFiltroOrdenDataTable(dataInicial = [], limiteInicial = null) {
    // Incializamos las referencias reactivas
    const dataOriginal = ref(dataInicial);
    const buscadorText = ref('');
    const ordenarPor = ref('');
    const ordenAscendente = ref(true);
    const limite = ref(limiteInicial);

    // Computed para filtrar y ordenar los datos
    const dataFiltrada = computed( () => {
        let result = [...dataOriginal.value];

        // Filtramos por el texto del buscador
        if (buscadorText.value) {
            const texto = buscadorText.value.toLowerCase();

            result = result.filter(item =>
                Object.values(item).some(value =>
                    value?.toString().toLowerCase().includes(texto)
                )
            );
        }

        // Limitamos (si es que hay limite) 
        let dataVisible = result
        if (limite.value !== null) {
            dataVisible = result.slice(0, limite.value);
        }

        // Ordenamos segun el campo y la direccion de orden solo los visibles
        if (ordenarPor.value) {
            dataVisible.sort( (a, b) => {
                let valorA = a[ordenarPor.value];
                let valorB = b[ordenarPor.value];

                if (!isNaN(valorA) && !isNaN(valorB)) {
                    valorA = Number(valorA);
                    valorB = Number(valorB);
                } else {
                    valorA = valorA?.toString().toLowerCase();
                    valorB = valorB?.toString().toLowerCase();
                }

                if (valorA < valorB) return ordenAscendente.value ? -1 : 1;
                if (valorA > valorB) return ordenAscendente.value ? 1 : -1;
                return 0;
            });
        }

        return dataVisible;
    });

    const ordenar = (campo) => {
        if (ordenarPor.value === campo) {
            ordenAscendente.value = !ordenAscendente.value; // Cambia la dirección de orden
        } else {
            ordenarPor.value = campo; // Establece el nuevo campo a ordenar
            ordenAscendente.value = true; // Resetea a orden ascendente
        }
    }

    return {
        dataOriginal,
        buscadorText,
        ordenarPor,
        ordenAscendente,
        dataFiltrada,
        limite,
        ordenar
    }
}