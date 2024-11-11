<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import SelectForm from '@/Components/SelectForm.vue';
import OptionForm from '@/Components/OptionForm.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import { Head, router } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Informes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2
                    class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Reportes
                </h2>
    
                <h3
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white text-sm"
                    @click="generarPDF"
                    >
                    Generar PDF
                </h3>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Aqui los Reportes por filtro -->
                        <div class="grid grid-cols-2 md:grid-cols-4">
                            <div class="p-2 md:p-4" >
                                <InputLabel for="fecha" value="Fecha:"/>
    
                                <TextInput
                                    id="fecha"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="buscador.fecha"
                                    required
                                    autofocus
                                    autocomplete="fecha"
                                />
                            </div>
                            <!--
                            <div class="p-2 md:p-4" >
                                <InputLabel for="hasta" value="Hasta:"/>

                                <TextInput
                                    id="hasta"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="buscador.hasta"
                                    required
                                    autofocus
                                    autocomplete="hasta"
                                    @change="validarFecha()"
                                />
                            </div>
                            -->
                            <div class="p-2 md:p-4" >
                                <InputLabel for="tienda" value="Tiendas:"/>

                                <SelectForm
                                    v-model="buscador.id_tienda"
                                    class="mt-1 block w-full"
                                >
                                    <OptionForm v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
                                        {{ tienda.nombre_tienda }}
                                    </OptionForm>
                                </SelectForm>
                            </div>
                            <div class="flex col-span-2 md:col-span-1 justify-end md:justify-start md:items-stretch">
                                <PrimaryButton
                                    class="self-end m-3 md:m-5 px-4 py-2 md:col-span-1"
                                    @click="getData()"
                                >
                                    Buscar
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Aqui la Tabla de Datos -->
                        <div class="py-3">
                            <div class="overflow-x-auto rounded-md shadow">
                                <Table>
                                    <thead>
                                        <TableTh>Plus</TableTh>
                                        <TableTh>Producto</TableTh>
                                        <TableTh v-for="(tienda, index) in dataThead" :key="index">
                                            {{ tienda.codigo }}
                                        </TableTh>
                                        <TableTh>Total</TableTh>
                                        <!--
                                        <TableTh>Total (KG)</TableTh>
                                        -->
                                    </thead>
                                    <TableBodyTr
                                        v-for="(producto, index) in pedidos" :key="producto.id_producto"
                                    >
                                        <TableBodyTd> {{ producto.plus }}</TableBodyTd>
                                        <TableBodyTd> {{ producto.producto }}</TableBodyTd>

                                        <!-- Iteramos sobre las tiendas y comparamos el id_tienda -->
                                        <TableBodyTd v-for="tienda in dataThead" :key="tienda.id_tienda">
                                            <!-- Accedemos dinámicamente a "pedido_[id_tienda]" en producto -->
                                            <span v-if="tienda.id_tienda !== 0" >
                                                {{ producto[`pedido_${tienda.id_tienda}`] || '-' }}
                                            </span>
                                        </TableBodyTd>

                                        <TableBodyTd> {{ producto.total }}</TableBodyTd>
                                    </TableBodyTr>
                                    
                                    <TableBodyTr v-if="pedidos.length === 0">
                                        <TableBodyTd :colspan="cols" class="font-semibold" >Sin Registros</TableBodyTd>
                                    </TableBodyTr>
                                </Table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
        pedidos: Array,
        tiendas: Array,
        dataThead: Array,
    },

    data() {
        return {
            buscador: {
                fecha: this.formatDate(new Date()),
                id_tienda: 0,
                type: 'null'
            },
        }
    },

    computed: {
        cols() {
            return this.dataThead.length + 3;
        },
    },

    /*
    watch: {
        form: {
            deep: true,
            handler: function () {
                setTimeout(() => {
                    console.log("Aqui se filtrará")
                }, 150)
            }
        }
    },
    */

    methods: {
        generarPDF() {
    const dataToSend = { 
        pedidos: this.pedidos,
        tiendas: this.tiendas,
        dataThead: this.dataThead,
        fecha: this.buscador.fecha
    };

    axios.post('/generatePDF', dataToSend, { responseType: 'blob' })
    .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Generar el nombre dinámicamente en el frontend usando this.buscador.fecha
        link.setAttribute('download', `Reporte-${this.buscador.fecha}.pdf`);
        document.body.appendChild(link);
        link.click();
    })
    .catch(error => {
        console.error('Error generando el PDF:', error);
    });
},

        formatDate(date) {
            let anio = date.getFullYear();
            let mes = (date.getMonth() + 1).toString().padStart(2, '0');
            let dia = date.getDate().toString().padStart(2, '0');

            return `${anio}-${mes}-${dia}`;
        },

        /*
        validarFecha(parametro = null) {
            let desde = new Date(this.buscador.desde);
            let hasta = new Date(this.buscador.hasta);

            if (hasta < desde) {
                if (parametro === 'desde') {
                    this.buscador.hasta = this.buscador.desde;
                } else {
                    this.buscador.desde = this.buscador.hasta;
                }
            }
        },
        */

        getData() {
            this.buscador.type = 'ShowDataTable';
            const data = router.get('/reports', {dates: this.buscador}, {preserveState: true})
        }
    },

    mounted() {
        console.log("pedidos")
        console.log(this.pedidos);
    }
}
</script>
