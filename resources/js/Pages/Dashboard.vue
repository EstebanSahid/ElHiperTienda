<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import DropdownHiper from '@/Components/DropdownHiper.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import { descargarPdfPedido } from '@/Services/DownloadPdfHelper';
import { dameFechaFormateada } from '@/Services/DateHelper';
import { onMounted } from 'vue'

const props = defineProps({
    tiendas: {
        type: Object,
    },
});

onMounted(() => {
   console.log('Datos de tiendas:', props.tiendas.data)
})

function iniciarDescargaPDF(tienda) {
    descargarPdfPedido(tienda);
}

</script>

<template>
    <Head title="Tiendas" />
    
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Generar Ordenes
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <!-- Table Index -->
                    <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                        <Table>
                            <thead>
                                <tr class="text-center font-bold">
                                    <TableTh>Codigo</TableTh>
                                    <TableTh>Nombre</TableTh>
                                    <TableTh>Registrado</TableTh>
                                    <!---->
                                    <TableTh>Acciones</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr  
                                    v-for="tienda in tiendas.data" :key="tienda.id_tienda"
                                >
                                    <TableBodyTd>
                                        {{ tienda.codigo }}
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        {{ tienda.nombre }}
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        {{ tienda.procesado ? 'Si' : 'No' }}
                                    </TableBodyTd>
                                    <!-- -->
                                    <TableBodyTd>
                                        <DropdownHiper 
                                                @open="mostrarDropdown = true" 
                                                @close="mostrarDropdown = false"
                                            >

                                                <template #trigger>
                                                    Acciones
                                                </template>

                                                <template #content>
                                                    <a
                                                        :href="tienda.procesado ? `/order/${tienda.id_tienda}/edit` : `/order/${tienda.id_tienda}/create`"
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 z-50">
                                                        {{ tienda.procesado ? 'Editar' : 'Generar' }}
                                                    </a>
                                                    <a 
                                                        v-if="tienda.procesado" 
                                                        :href="`/order/${tienda.id_pedido}/view`"
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 z-50" 
                                                    >
                                                        Ver orden
                                                    </a>
                                                    <a 
                                                        v-if="tienda.procesado" 
                                                        href="#"
                                                        @click.prevent="iniciarDescargaPDF(tienda)" 
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 z-50"
                                                    >
                                                        Descargar PDF
                                                    </a>
                                                    <!-- <a 
                                                        v-if="$page.props.auth.user.id_rol === $page.props.enums.Rol.ADMINISTRADOR" 
                                                        href="#" 
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-white 
                                                            hover:bg-gray-100 dark:hover:bg-gray-600 z-50"
                                                    >
                                                        Auditoria
                                                    </a> -->
                                                </template>
                                            </DropdownHiper>

                                        <!-- <Link
                                            :href="tienda.procesado ? `/order/${tienda.id_tienda}/edit` : `/order/${tienda.id_tienda}/create`"
                                            class="p-2 button-hiper inline-flex items-center rounded-md border border-transparent bg-[#97a907]
                                                    px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-[#59650f]
                                                    focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
                                                    active:bg-gray-900 dark:text-gray-800 dark:focus:bg-white dark:focus:ring-offset-gray-800 
                                                    dark:active:bg-gray-300dark:bg-[#97a907] dark:hover:bg-[#eef85e]"
                                        >
                                            {{ tienda.procesado ? 'Editar' : 'Generar' }}
                                        </Link> -->
                                    </TableBodyTd>
                                    
                                </TableBodyTr>

                                <TableBodyTr v-if="tiendas.data.length === 0">
                                    <TableBodyTd colspan="4"> No hay tiendas registradas.</TableBodyTd>
                                </TableBodyTr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
            <!-- Paginación -->
            <Pagination :links="tiendas.links" />
        </div>
    </AuthenticatedLayout>
</template>

