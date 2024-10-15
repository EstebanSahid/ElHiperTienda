<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';

defineProps({
    tiendas: {
        type: Array,
    },
});

</script>

<template>
    <Head title="Tiendas" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Tiendas
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
                                    <TableTh>Acciones</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr  
                                    v-for="tienda in tiendas.data" :key="tienda.id_tienda"
                                >
                                    <TableBodyTd>{{ tienda.codigo }}</TableBodyTd>
                                    <TableBodyTd>{{ tienda.nombre }}</TableBodyTd>
                                    <TableBodyTd>{{ tienda.procesado ? 'Si' : 'No' }}</TableBodyTd>
                                    <TableBodyTd>
                                        <div v-if="tienda.procesado == 0">
                                            <PrimaryButton
                                                class="ms-4">
                                                <Link :href="route('order.create')">
                                                    Generar
                                                </Link>
                                            </PrimaryButton>
                                        </div>
                                        <div v-else>
                                            <PrimaryButton class="mr-3">Editar</PrimaryButton>
                                            <PrimaryButton>Visualizar</PrimaryButton>
                                        </div>
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

<script>
export default {
    methods: {
        
    }
}
</script>
