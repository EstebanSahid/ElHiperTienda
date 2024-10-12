<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectForm from '@/Components/SelectForm.vue';
import OptionForm from '@/Components/OptionForm.vue';
import { Head, useForm } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Registrar Usuario
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
                                    <InputLabel for="name" value="Nombre" />
    
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
    
                                <div>
                                    <InputLabel for="telefono" value="Telefono" />
    
                                    <TextInput
                                        id="telefono"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.telefono"
                                        required
                                        autocomplete="username"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.telefono" />
                                </div>
    
                                <div class="mt-4">
                                    <InputLabel for="email" value="Email" />
    
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        v-model="form.email"
                                        required
                                        autocomplete="username"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
    
                                <div class="mt-4">
                                    <InputLabel for="password" value="Contraseña" />
    
                                    <TextInput
                                        id="password"
                                        type="password"
                                        class="mt-1 block w-full"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                    />
    
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="id_rol" value="Rol" />

                                    <SelectForm
                                        v-model="form.id_rol"
                                        class="mt-1 block w-full"
                                    >
                                        <OptionForm v-for="rol in roles" :key="rol.id_rol" :value="rol.id_rol">
                                            {{ rol.descripcion }}
                                        </OptionForm>
                                    </SelectForm>
                                </div>
    
                                <div class="mt-4">
                                    <InputLabel
                                        for="password_confirmation"
                                        value="Confirmar Contraseña"
                                    />
    
                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                    />
    
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.password_confirmation"
                                    />
                                </div>
                            </div>

                            <div class="border-t-2 mt-6 dark:border-gray-900 border-gray-200" v-if="form.id_rol != 1">
                                <h4
                                    class="mt-4 font-semibold leading-tight text-gray-800 dark:text-gray-200"
                                >
                                    Asigar Tiendas
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mt-4">
                                        <InputLabel for="Permisos" value="Permisos" />

                                        <SelectForm
                                            v-model="prueba"
                                            class="mt-1 block w-full"
                                        >
                                            <OptionForm v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
                                                {{ tienda.nombre_tienda }}
                                            </OptionForm>
                                        </SelectForm>
                                    </div>
                                    <div class="mt-4">
                                        
                                    </div>
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
    props: {
        roles: Array,
        tiendas: Array,
    },
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                email: '',
                telefono: '',
                password: '',
                password_confirmation: '',
                id_rol: 1,
            }),
            prueba: 1,
        }
    },
    
    methods: {
        store() {
            this.form.post('/users')
        },
    },

    mounted() {
        console.log(this.tiendas)
    }
}
</script>
