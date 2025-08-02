<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectForm from '@/Components/SelectForm.vue';
import OptionForm from '@/Components/OptionForm.vue';
import InputLabel from '@/Components/InputLabel.vue';
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
                    
                    <div class="grid-cols-2">
                        <!-- <div class="overflow-x-auto rounded-md shadow pt-3"> -->
                        <div class="overflow-x-auto rounded-md mt-4">
                            <input type="file" name="file" id="file" accept=".xlsx" @change="obtenerHojasExcel()" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="HojaExcel" value="Hoja de Excel" />

                            <SelectForm
                                v-model="excel.hoja"
                                class="mt-1 block w-full"
                                
                            >

                                <OptionForm :value="''">
                                    {{ hojasExcel.length === 0 ? 'Carga un Excel para poder listar las Hojas' : 'Por favor Selecciona una Hoja' }}
                                </OptionForm>
                                
                                <OptionForm v-for="hoja in hojasExcel" :key="hoja.id_hoja" :value="hoja.nombre_hoja">
                                    {{ hoja.nombre_hoja }}
                                </OptionForm>
                            </SelectForm>

                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <PrimaryButton
                        class="ms-4"
                        @click="ImportarExcel()"
                        v-if="excel.file !== '' && excel.hoja !== ''"
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
                    this.hojasExcel = response.data.hojas;

                    if (this.hojasExcel.length > 0) {
                        this.excel.hoja = this.hojasExcel[0].nombre_hoja;
                    } else {
                        alert('No se encontraron hojas en el archivo.');
                    }
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