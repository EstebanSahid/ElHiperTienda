<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import { Head, Link } from '@inertiajs/vue3';
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
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Aqui los usuarios para su CRUD
                    </div>
                </div>
            </div>
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