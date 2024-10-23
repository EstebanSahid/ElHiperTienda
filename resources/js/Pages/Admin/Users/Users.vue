<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';

defineProps({
    users: {
        type: Array
    }
});
</script>

<template>
    <Head title="Informes" />

    <Notification />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Lista de Usuarios
                </h2>
                <Link
                    :href="route('registro.user')"
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Nuevo Usuario
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
                                    <TableTh>Rol</TableTh>
                                    <TableTh>Correo</TableTh>
                                    <TableTh>Telefono</TableTh>
                                    <TableTh>Estado</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr v-for="user in users.data" :key="user.id">
                                    <TableBodyTd>
                                        <Link :href="`/users/${user.id}/edit`"> 
                                            {{ user.name }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/users/${user.id}/edit`"> 
                                            {{ user.descripcion }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/users/${user.id}/edit`"> 
                                            {{ user.email }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/users/${user.id}/edit`"> 
                                            {{ user.telefono }}
                                        </Link>
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <Link :href="`/users/${user.id}/edit`"> 
                                            {{ user.estado }}
                                        </Link>
                                    </TableBodyTd>
                                </TableBodyTr>
                                
                                <TableBodyTr v-if="users.data.length === 0">
                                    <TableBodyTd colspan="4"> No hay usuarios registrados.</TableBodyTd>
                                </TableBodyTr>
                            </tbody>
                        </Table>

                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <Pagination :links="users.links" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
        flash: Object,
    },
};
</script>