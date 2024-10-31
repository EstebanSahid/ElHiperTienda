<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    unidades: {
        type: Object
    }
});
</script>

<template>
    <Head title="Unidades de Medida" />

    <Notification />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Unidades de Medida
                </h2>
                <Link
                    :href="route('registro.unidad')"
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white text-sm"
                >
                    Nueva Unidad
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                        <Table>
                            <thead>
                                <tr class="text-center font-bold">
                                    <TableTh>Descripcion</TableTh>
                                    <TableTh>Codigo</TableTh>
                                    <TableTh>Estado</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr v-for="unidad in unidades.data" :key="unidad.id_unidad_pedido">
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/unidad/${unidad.id_unidad_pedido}/edit`"> 
                                            {{ unidad.descripcion }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/unidad/${unidad.id_unidad_pedido}/edit`"> 
                                            {{ unidad.codigo }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/unidad/${unidad.id_unidad_pedido}/edit`"> 
                                            {{ unidad.estado }}
                                        </Link>
                                    </TableBodyTd>
                                </TableBodyTr>
                                
                                <TableBodyTr v-if="unidades.data.length === 0">
                                    <TableBodyTd colspan="3"> No hay unidades registrados.</TableBodyTd>
                                </TableBodyTr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
            <!-- Paginación -->
            <Pagination :links="unidades.links" />
        </div>
    </AuthenticatedLayout>
</template>
