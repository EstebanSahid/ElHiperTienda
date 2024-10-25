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
    <Head title="Editar Unidad de medida" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Modificar Unidad de Medida
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
        unidad: Object,
    },

    data() {
        return {
            form: this.$inertia.form({
                descripcion: this.unidad.descripcion,
                codigo: this.unidad.codigo,
                estado: this.unidad.estado,
                id_unidad_pedido: this.unidad.id_unidad_pedido
            }),
        }
    },

    methods: {
        update() {
            this.form.put('/unidadEdit');
        },

        destroy() {
            this.form.put('/unidadDelete');
        }
    },
}
</script>
