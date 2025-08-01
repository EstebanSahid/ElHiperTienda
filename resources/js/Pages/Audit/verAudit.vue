<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Auditoria" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Auditoria
                </h2>
            </div>
        </template>
    </AuthenticatedLayout>
</template>