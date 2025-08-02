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
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLinkButton from '@/Components/DropdownLinkButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
</script>

<template>
    <Head title="Informes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reportes
                </h2>
    
                <div v-if="pedidos.length > 0" >
                    <Dropdown>
                        <template #trigger>
                            <span>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-md text-sm dark:text-gray-400 dark:hover:text-gray-100 text-gray-600 hover:text-gray-900 transition duration-150 ease-in-out"
                                >
                                    Opciones
                                    <svg
                                        class="-me-0.5 ms-2 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLinkButton @click="generarPDF">
                                Generar PDF
                            </DropdownLinkButton>
                            <!-- <DropdownLinkButton v-if="buscador.id_tienda !== 0" @click="duplicarOrden" >
                                Duplicar Orden
                            </DropdownLinkButton> -->
                        </template>
                    </Dropdown>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
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
                                    autocomplete="fecha"
                                />
                            </div>                            
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

        <div 
            v-if="showModal"
            class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border-2 dark:border-gray-900 border-gray-100 w-96 max-w-full">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Contenido del Modal -->
                    <div class="py-3">
                        <div class="overflow-x-auto rounded-md shadow">
                            <Table>
                                <thead>
                                    <TableTh>Seleccione la Tienda</TableTh>
                                </thead>
                                <TableBodyTr
                                    v-for="(tiendaDuplicar) in tiendasDuplicar"
                                    :key="tiendaDuplicar.id_tienda"
                                >
                                    <TableBodyTd>
                                        <Link :href="`/order/${idPedido}/${tiendaDuplicar.id_tienda}/duplicate`"
                                            class="block w-full h-full px-4 py-2 text-center"
                                        >
                                            {{ tiendaDuplicar.nombre_tienda }}
                                        </Link>
                                    </TableBodyTd>
                                </TableBodyTr>
                                <TableBodyTr v-if="tiendasDuplicar.length === 0">
                                    <TableBodyTd
                                        colspan="1"
                                        class="font-semibold"
                                    >
                                        Todas las tiendas ya tienen una orden registrada para hoy
                                    </TableBodyTd>
                                </TableBodyTr>
                            </Table>
                        </div>
                    </div>

                    <!-- Botón para cerrar el modal -->
                    <div class="pt-2 flex justify-end">
                        <DangerButton
                            class="ms-4"
                            @click="showModal = false"
                        >
                            Cerrar
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
        tiendasDuplicar: Array,
        pedidos: Array,
        tiendas: Array,
        dataThead: Array,
        numerosPedido: Array,
    },

    data() {
        const urlParams = new URLSearchParams(window.location.search);
        const fechaParam = urlParams.get('dates[fecha]');
        const idTiendaParam = urlParams.get('dates[id_tienda]') || 0;

        return {
            buscador: {
                fecha: fechaParam ? fechaParam : this.formatDate(new Date()),
                id_tienda: idTiendaParam
            },

            disabledPDF: true,
            showModal: false,

            idPedido: null,
        }
    },

    computed: {
        cols() {
            return this.dataThead.length + 3;
        },
    },

    mounted() {
        console.log("Tiendas Duplicar")
        console.log(this.pedidos);
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
        formatDate(date) {
            let anio = date.getFullYear();
            let mes = (date.getMonth() + 1).toString().padStart(2, '0');
            let dia = date.getDate().toString().padStart(2, '0');

            return `${anio}-${mes}-${dia}`;
        },

        // Obtener los datos para el reporte
        getData() {
            const data = router.get('/reports', {dates: this.buscador}, {preserveState: true})
        },

        // Duplicar la orden
        duplicarOrden() {
            this.showModal = true
            this.idPedido = this.pedidos[0].id_pedido
            console.log("Pedido", this.pedidos[0].id_pedido)
            console.log(this.pedidos)
            console.log(this.pedidos[0].id_pedido)
            console.log('Aqui se duplicara la orden para a tienda ' + this.buscador.id_tienda);
        }
    },
}
</script>
