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
    tiendas: {
        type: Array
    }
});
</script>

<template>
    <Head title="Tiendas" />

    <Notification />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Lista de Tiendas
                </h2>
                <Link
                    :href="route('registro.store')"
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Nueva Tienda
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
                                    <TableTh>Nombre</TableTh>
                                    <TableTh>Codigo</TableTh>
                                    <TableTh>Telefono</TableTh>
                                    <TableTh>Direccion</TableTh>
                                    <TableTh>Estado</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr v-for="tienda in tiendas.data" :key="tienda.id_tienda">
                                    <TableBodyTd>
                                        <Link :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.nombre }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.codigo }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.telefono }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.direccion }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.estado }}
                                        </Link>
                                    </TableBodyTd>
                                </TableBodyTr>
                                
                                <TableBodyTr v-if="tiendas.data.length === 0">
                                    <TableBodyTd colspan="5"> No hay Tiendas registradas.</TableBodyTd>
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

<script>
export default {
    props: {
        flash: Object
    }
}
</script>