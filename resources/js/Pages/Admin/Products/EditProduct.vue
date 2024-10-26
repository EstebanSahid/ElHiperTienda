<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Actualizar producto
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
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
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.plus"
                                    required
                                    autocomplete="plus"
                                />

                                <InputError class="mt-2" :message="form.errors.plus" />
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between">
                            <div class="order-first">
                                <DangerButton
                                    class="ms-4"
                                    @click="destroy()"
                                    v-if="form.estado == 'Activo'"
                                >
                                    Eliminar
                                </DangerButton>
                            </div>
                            <div class="order-last">
                                <PrimaryButton
                                    class="ms-4"
                                    @click="update()"
                                >
                                    {{ form.estado == 'Activo' ? 'Actualizar' : 'Actualizar y Reestablecer' }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
        producto: Object,
    },

    data() {
        return {
            form: this.$inertia.form({
                plus: this.producto.plus,
                nombre: this.producto.nombre,
                estado: this.producto.estado,
                id_producto: this.producto.id_producto
            }),
        }
    },

    methods: {
        update() {
            this.form.put('/productEdit');
        },

        destroy() {
            this.form.put('/productDelete');
        }
    },
}
</script>