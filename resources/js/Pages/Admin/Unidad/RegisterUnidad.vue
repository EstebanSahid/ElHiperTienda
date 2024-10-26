<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Nueva Unidad de Medida" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Registrar Unidad de Medida
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="store">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="descripcion" value="Unidad" />

                                    <TextInput
                                        id="descripcion"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.descripcion"
                                        required
                                        autofocus
                                        autocomplete="descripcion"
                                    />

                                    <InputError class="mt-2" :message="form.errors.descripcion" />
                                </div>

                                <div>
                                    <InputLabel for="codigo" value="Codigo" />

                                    <TextInput
                                        id="codigo"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.codigo"
                                        required
                                        autocomplete="codigo"
                                    />

                                    <InputError class="mt-2" :message="form.errors.codigo" />
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <PrimaryButton
                                    class="ms-4"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Registrar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                descripcion: '',
                codigo: ''
            })
        }
    },

    methods: {
        store() {
            this.form.post('/unidadMedida');
        }
    }
}
</script>
