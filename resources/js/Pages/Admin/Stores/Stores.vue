<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import { Head, Link } from '@inertiajs/vue3';
import ModalImportExcel from '@/Components/ModalImportExcel.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import DropdownLinkButton from '@/Components/DropdownLinkButton.vue';

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

                <Dropdown>
                    <template #trigger>
                        <span>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md text-sm dark:text-gray-400 dark:hover:text-gray-100 text-gray-600 hover:text-gray-900 transition duration-150 ease-in-out"
                            >
                                Registro
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
                        <DropdownLink :href="route('registro.store')">
                            Nueva Tienda
                        </DropdownLink>
                        <DropdownLinkButton @click="showModal = true">
                            Importar desde Excel
                        </DropdownLinkButton>
                    </template>
                </Dropdown>

                <!-- <Link
                    :href="route('registro.store')"
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white text-sm"
                >
                    Nueva Tienda
                </Link> -->
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
                                        <Link class="w-full h-full block" :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.nombre }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.codigo }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.telefono ?? '-' }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/tiendas/${tienda.id_tienda}/edit`"> 
                                            {{ tienda.direccion ?? '-' }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link class="w-full h-full block" :href="`/tiendas/${tienda.id_tienda}/edit`"> 
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

        <ModalImportExcel v-if="showModal"
            @CerrarModal="showModal = false"
            :title="'Importar Tiendas'"
            :message="'El archivo Excel debe tener en sus cabeceras el nombre y el código de la tienda.'"
            :rutaApi="'/importarTiendasExcel'"
        />

    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
        flash: Object
    },

    data() {
        return {
            showModal: false,
        }
    }
}
</script>