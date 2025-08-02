<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    productos: {
        type: Object
    },

    /*
    filtro: {
        type: Object
    }
    */
});
</script>

<template>
    <Head title="Actualizar Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Lista de Productos
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                        <!-- Buscador 
                        <div class="p-3 mx-2 grid grid-cols-4 md:grid-cols-6">
                            <div class="col-span-2">
                                <InputLabel for="search" value="Buscar Producto" />
        
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="buscador.search"
                                    required
                                />
                            </div>
                        </div>
                        -->
                        <Table>
                            <thead>
                                <tr class="text-center font-bold">
                                    <TableTh class="cursor-pointer" @click="ordenarPor('Plus')">Plus</TableTh>
                                    <TableTh class="cursor-pointer" @click="ordenarPor('nombre')">Nombre</TableTh>
                                    <TableTh>Estado</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr v-for="producto in productos" :key="producto.id_producto">
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/productos/${producto.id_producto}/edit`"> 
                                            {{ producto.plus }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/productos/${producto.id_producto}/edit`"> 
                                            {{ producto.nombre }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/productos/${producto.id_producto}/edit`"> 
                                            {{ producto.estado }}
                                        </Link>
                                    </TableBodyTd>
                                </TableBodyTr>
                                
                                <TableBodyTr v-if="productos.length === 0">
                                    <TableBodyTd colspan="3"> No hay Productos registrados.</TableBodyTd>
                                </TableBodyTr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
            <!-- Paginación 
            <Pagination :links="productos.links" />
            -->
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            /*
            buscador: {
                search: this.filtro.search,
                orderBy: this.filtro.orderBy
            },
            */
        }
    },
    /*
    watch: {
        buscador: {
            deep: true,
            handler: function () {
                setTimeout(() => {
                    this.getData();
                    // router.get(`/products`, {search: this.buscador.search }, { preserveState: true });
                }, 150)
            }
        }
    },
    */

    methods: {
        ordenarPor(value) {
            this.buscador.orderBy = value
            //this.getData();
        },

        getData() {
            router.get(`/products`, {search: this.buscador }, { preserveState: true });
        }
    },

    mounted() {
    }
}
</script>
