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
                class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200"
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
                        <form @submit.prevent="validacion">
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
                                
                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    <ul class="text-sm font-medium border
                                            text-gray-900 bg-white border-gray-200 rounded-lg 
                                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">

                                        <li v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda" 
                                            class="w-full border-b border-gray-300 rounded-t-lg dark:border-gray-700">
                                            <div class="flex items-center ps-3">
                                                <input 
                                                    id="vue-checkbox-{{ tienda.id_tienda }}" type="checkbox" 
                                                    :value="tienda.id_tienda" v-model="form.tiendasAsignadas"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <label for="vue-checkbox-{{tienda.id_tienda}}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ tienda.nombre_tienda }}</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p
                                        v-if="validacionPermisos"
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        Se necesita Asignar Tiendas para Continuar
                                    </p>
                                </Transition>
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
                tiendasAsignadas: [],
            }),
            validacionPermisos: false,
        }
    },
    
    methods: {
        store() {
            this.form.post('/users');
        },

        validacion() {
            // Minimo una tienda si el usuario es encargado
            if (this.form.id_rol != 1 && this.form.tiendasAsignadas.length == 0) {
                this.validacionPermisos = true;
                this.form.processing = false;
                return;
            } 
            
            // Permitir la Validacion de permisos y limpiar array de tiendas
            this.validacionPermisos = false;
            if (this.form.id_rol == 1) {
                this.form.tiendasAsignadas = [];
            }

            // llamado al guardar
            this.store()
        },
    },
}
</script>
