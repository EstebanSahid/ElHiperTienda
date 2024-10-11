<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodytr from '@/Components/TableBodytr.vue';
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

        <div class="py-12 z-0">
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
                                    <TableTh>Acciones</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodytr  
                                    v-for="tienda in tiendas" :key="tienda.id"
                                >
                                        <TableBodyTd>
                                            {{ tienda.codigo }}
                                        </TableBodyTd>
                                    
                                        <TableBodyTd>
                                            {{ tienda.nombre }}
                                        </TableBodyTd>

                                        <TableBodyTd>
                                            <div v-if="tienda.procesado == 0">
                                                <PrimaryButton>
                                                    <a :href="route('order.create')">Generar</a>
                                                </PrimaryButton>
                                            </div>
                                            <div v-else>
                                                <PrimaryButton class="mr-3">Editar</PrimaryButton>
                                                <PrimaryButton>Visualizar</PrimaryButton>
                                            </div>
                                        </TableBodyTd>
                                </TableBodytr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>

    
</template>
