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
import { Head, router, Link } from '@inertiajs/vue3';
import DropdownHiper from '@/Components/DropdownHiper.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { dameFechaFormateada } from '@/Services/DateHelper';
</script>

<template>
    <Head title="Historial" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Historial de ordenes
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Aqui los Reportes por filtro -->
                        <div class="grid grid-cols-2 md:grid-cols-4">
                            <!-- <div class="p-2 md:p-4" >
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
                            </div>                             -->
                            <div class="p-2 md:p-4" >
                                <InputLabel for="tienda" value="Tiendas:"/>

                                <SelectForm
                                    v-model="buscador.id_tienda"
                                    class="mt-1 block w-full"
                                >
                                    <OptionForm v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
                                        {{ tienda.nombre }}
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
                            <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                                <Table>
                                    <thead>
                                        <TableTh>N° Pedido</TableTh>
                                        <TableTh>Fecha</TableTh>
                                        <TableTh>Tienda</TableTh>
                                        <TableTh>Acciones</TableTh>
                                    </thead>
                                    <TableBodyTr
                                        v-for="orden in ordenes.data" :key="orden.id_pedido" :value="orden.id_pedido"
                                    >
                                        <TableBodyTd> {{ orden.numero_pedido }}</TableBodyTd>
                                        <TableBodyTd> {{ dameFechaFormateada(orden.fecha_pedido) }}</TableBodyTd>
                                        <TableBodyTd> {{ orden.nombre_tienda }}</TableBodyTd>
                                        <TableBodyTd class="w-48 z-50">
                                            <DropdownHiper 
                                                @open="mostrarDropdown = true" 
                                                @close="mostrarDropdown = false"
                                            >

                                                <template #trigger>
                                                    Acciones
                                                </template>

                                                <template #content>
                                                    <a :href="`/order/${orden.id_pedido}/view`" class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 z-50">Ver orden</a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 z-50">Descargar PDF</a>
                                                    <a 
                                                        v-if="$page.props.auth.user.id_rol === $page.props.enums.Rol.ADMINISTRADOR" 
                                                        href="#" 
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-white 
                                                            hover:bg-gray-100 dark:hover:bg-gray-600 z-50"
                                                    >
                                                        Auditoria
                                                    </a>
                                                </template>
                                            </DropdownHiper>
                                        </TableBodyTd>
                                    </TableBodyTr>
                                    
                                    <TableBodyTr v-if="ordenes.data.length === 0">
                                        <TableBodyTd colspan="4" class="font-semibold" >Sin Registros</TableBodyTd>
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
        ordenes: Array,
        tiendas: Array,
    },

    data() {
        const urlParams = new URLSearchParams(window.location.search);
        const idTiendaParam = urlParams.get('dates[id_tienda]') || 0;

        return {
            buscador: {
                id_tienda: idTiendaParam
            },
            mostrarDropDown: false,
        }
    },

    computed: {
        // cols() {
        //     return this.dataThead.length + 3;
        // },
    },

    mounted() {
    },

    methods: {
        // Generar el PDF
        generarPDF() {
            const dataToSend = { 
                pedidos: this.pedidos,
                tiendas: this.tiendas,
                dataThead: this.dataThead,
                fecha: this.buscador.fecha,
                numerosPedido: this.numerosPedido,
            };

            axios.post('/generatePDF', dataToSend, { responseType: 'blob' })
            .then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', `Reporte-${this.buscador.fecha}.pdf`);
                document.body.appendChild(link);
                link.click();
            })
            .catch(error => {
                console.error('Error generando el PDF:', error);
            });
        },

        // Formatear la fecha para que sea valido en el input de la fecha
        /* formatDate(date) {
            let anio = date.getFullYear();
            let mes = (date.getMonth() + 1).toString().padStart(2, '0');
            let dia = date.getDate().toString().padStart(2, '0');

            return `${anio}-${mes}-${dia}`;
        },*/

        // Obtener los datos para el reporte
        getData() {
            const data = router.get('/orders', {dates: this.buscador}, {preserveState: true})
        },
    },
}
</script>
