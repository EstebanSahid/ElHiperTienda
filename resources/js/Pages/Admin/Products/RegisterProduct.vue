<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Notificacion from '@/Components/Notification.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Nuevo Producto" />

    <Notificacion />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Registrar Producto
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
                                    <InputLabel for="nombre" value="Nombre" />
    
                                    <TextInput
                                        id="nombre"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.nombre"
                                        required
                                        autofocus
                                        autocomplete="nombre"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.nombre" />
                                </div>
    
                                <div>
                                    <InputLabel for="plus" value="Plus" />
    
                                    <TextInput
                                        id="plus"
                                        type="number"
                                        @keyup="validarMayorCero()"
                                        min="1"
                                        class="mt-1 block w-full"
                                        v-model="form.plus"
                                        required
                                        autocomplete="plus"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.plus" />
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
                nombre: '',
                plus: '',
            })
        }
    },

    methods: {
        store() {
            this.form.post('/products');
        },

        validarMayorCero(){
            if (this.form.plus < 1) {
                this.form.plus = null;
            }
        },
    }
}
</script>
