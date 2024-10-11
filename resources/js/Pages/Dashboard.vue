<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

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
                    <!--
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Aqui las Tiendas
                    </div>
                    -->
                    <!-- Table Index -->
                    <div class="bg-white rounded-md shadow overflow-x-auto">
                        <table class="w-full whitespace-nowrap" >
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="pb-4 pt-6 px-6">Codigo</th>
                                    <th class="pb-4 pt-6 px-6">Nombre</th>
                                    <th class="pb-4 pt-6 px-6" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr  
                                    v-for="tienda in tiendas" :key="tienda.id"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100"
                                >
                                        <td class="border-t px-6 py-4">
                                            {{ tienda.codigo }}
                                        </td>
                                    
                                    
                                        <td class="border-t px-6 py-4">
                                            {{ tienda.nombre }}
                                        </td>

                                        <td class="border-t px-6 py-4">
                                            Agregar/Editar/Ver
                                        </td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>

    
</template>
