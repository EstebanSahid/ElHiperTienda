<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
                        <input type="file" name="file" id="file" accept=".xlsx" @change="validarExcel()" />
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
        headers: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            // excel: {
            //     file: '',
            //     headers: [],
            //     data: [],
            // },

            // Excel
            excel: this.$inertia.form({
                file: '',
                headers: [],
            }),
        }
    },

    methods: {
        ImportarExcel() {
            this.excel.post(this.rutaApi);
            this.CerrarModal();
        },

        validarExcel() {
            this.excel.file = document.getElementById('file').files[0];
        },

        CerrarModal() {
            this.$emit('CerrarModal');
        },
    },
}
</script>