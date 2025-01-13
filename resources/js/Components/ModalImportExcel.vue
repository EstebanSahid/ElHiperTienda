<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {router} from '@inertiajs/vue3';
</script>

<template>
    <div 
        
        class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border-2 dark:border-gray-900 border-gray-100 w-auto max-w-full">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="py-3">
                    <h4 class="text-lg">{{ title }}</h4>
                    <p class="text-sm py-2">{{ message }}</p>
                    <div class="overflow-x-auto rounded-md shadow pt-3">
                        <input type="file" name="file" id="file" accept=".xlsx" @change="obtenerHojasExcel()" />
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <PrimaryButton
                        class="ms-4"
                        @click="ImportarExcel()"
                        v-if="excel.file"
                    >
                        Importar
                    </PrimaryButton>


                    <DangerButton
                        class="ms-4"
                        @click="CerrarModal()"
                    >
                        Cerrar
                    </DangerButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props :{
        title: {
            type: String,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
        rutaApi: {
            type: String,
            required: true,
        },
        hojas: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            // Excel
            excel: this.$inertia.form({
                file: '',
                hoja: '',
                headers: [],
            }),

            hojasExcel: [],
        }
    },

    methods: {
        ImportarExcel() {
            this.excel.post(this.rutaApi);
            this.CerrarModal();
        },

        obtenerHojasExcel() {
            this.excel.file = document.getElementById('file').files[0];

            if (!this.excel.file) {
                alert("Por favor, selecciona un archivo Excel.");
                return;
            }

            const formData = new FormData();
            formData.append('file', this.excel.file);

            axios
                .post('/ObtenerHojasExcel', formData)
                .then((response) => {
                    this.hojasExcel = response.data.hojas; // Asigna las hojas recibidas al array
                    console.log('Hojas obtenidas:', this.hojasExcel);
                })
                .catch((error) => {
                    console.error('Error al obtener las hojas:', error);
                    alert('Hubo un problema al procesar el archivo.');
                });
        },

        CerrarModal() {
            this.$emit('CerrarModal');
        },
    },
}
</script>