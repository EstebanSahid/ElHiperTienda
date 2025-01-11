<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

</script>

<template>
    <!-- 
        La ruta del Api, al modelo que se va a importar
        El mensaje de cabeceras que debe tener el excel

        v-if="showModal"
    -->
    <div 
        
        class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border-2 dark:border-gray-900 border-gray-100 w-auto max-w-full">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="py-3">
                    <h4 class="text-lg">{{ title }}</h4>
                    <p class="text-sm">{{ message }}</p>
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
        route: {
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
            //     file: null,
            //     headers: [],
            //     data: [],
            // },

            // Excel
            excel: this.$inertia.form({
                file: '',
            }),
        }
    },

    methods: {
        ImportarExcel() {
            this.excel.post('/import');
        },

        validarExcel() {
            this.excel.file = document.getElementById('file').files[0];
        },

        CerrarModal() {
            this.$emit('CerrarModal');
        },

        // validarExcel() {
        //     const file = document.getElementById('file').files[0];
        //     const reader = new FileReader();
        //     reader.onload = (e) => {
        //         const data = new Uint8Array(e.target.result);
        //         const workbook = XLSX.read(data, { type: 'array' });
        //         const sheet = workbook.Sheets[workbook.SheetNames[0]];
        //         const headers = [];
        //         for (let key in sheet) {
        //             if (key[0] === 'A') {
        //                 headers.push(sheet[key].v);
        //             }
        //         }
        //         this.excel.file = file;
        //         this.excel.headers = headers;
        //     }
        //     reader.readAsArrayBuffer(file);
        // },

        // ImportarExcel() {
        //     const formData = new FormData();
        //     formData.append('file', this.excel.file);
        //     axios.post(this.route, formData, {
        //         headers: {
        //             'Content-Type': 'multipart/form-data',
        //         },
        //     }).then((response) => {
        //         this.showModal = false;
        //         this.$inertia.reload();
        //     }).catch((error) => {
        //         console.log(error);
        //     });
        // },
    },
}
</script>