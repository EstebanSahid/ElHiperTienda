<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import EditProduct from './EditProduct.vue';

defineProps({
    productos: {
        type: Object
    },

    filtro: {
        type: Object
    }
});
</script>

<template>
    <Head title="Productos" />

    <Notification />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Lista de Productos
                </h2>
                <!--
                    -->
                <Link
                    :href="route('registro.product')"
                    class="rounded-md px-2 leading-tight text-black ring-1 ring-transparent transition 
                    hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] 
                    dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white text-sm"
                >
                    Nuevo Producto
                </Link>

                <!--
                <div class="relative">
                    <Dropdown align="right">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                >
                                    Opciones Avanzadas
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
                            <DropdownLink :href="route('registro.product')">
                                Nuevo Producto
                            </DropdownLink>
                            <DropdownLink :href="route('products')">
                                Carga Masiva de Productos
                            </DropdownLink>
                            <DropdownLink :href="route('edit.massive.product')">
                                Actualización Masiva
                            </DropdownLink>
                        </template>
                        
                    </Dropdown>
                </div>
                -->
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                        <!-- Buscador -->
                        <div class="p-3 mx-2 grid grid-cols-4 md:grid-cols-6">
                            <div class="col-span-2">
                                <InputLabel for="search" value="Buscar Producto" />
        
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="buscador.search"
                                    required
                                />
                            </div>
                        </div>
                        <Table>
                            <thead>
                                <tr class="text-center font-bold">
                                    <TableTh class="cursor-pointer" @click="ordenarPor('Plus')">Plus</TableTh>
                                    <TableTh class="cursor-pointer" @click="ordenarPor('nombre')">Nombre</TableTh>
                                    <TableTh>Estado</TableTh>
                                </tr>
                            </thead>
                            <tbody>
                                <TableBodyTr v-for="(producto, index) in productos.data" :key="producto.id_producto">
                                    <TableBodyTd>
                                        <div v-if="editableId !== producto.id_producto || editableCelda !== 'plus'" @click="enableEditable(producto, 'plus')">
                                            {{ producto.plus }}
                                        </div>

                                        <TextInput
                                            v-else
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="producto.plus"
                                            autofocus
                                            @blur="editPlus(producto.plus, index)"
                                        />
                                        <!--
                                        <Link class="w-full h-full block" :href="`/productos/${producto.id_producto}/edit`"> 
                                            {{ producto.plus }}
                                        </Link>
                                        -->
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <div v-if="editableId !== producto.id_producto || editableCelda !== 'nombre'" @click="enableEditable(producto, 'nombre')">
                                            {{ producto.nombre }}
                                        </div>
                                        <TextInput
                                            v-else
                                            id="plus"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="producto.nombre"
                                            required
                                            autocomplete="plus"
                                            @blur="editNombre(producto.nombre)"
                                        />
                                        <!--
                                        <Link class="w-full h-full block" :href="`/productos/${producto.id_producto}/edit`"> 
                                            {{ producto.nombre }}
                                        </Link>
                                        -->
                                    </TableBodyTd>
                                    <TableBodyTd>
                                        <div @click="producto.estado == 'Activo' ? deactivate(producto) : activate(producto)">
                                            {{ producto.estado }}
                                        </div>
                                    </TableBodyTd>
                                </TableBodyTr>
                                
                                <TableBodyTr v-if="productos.data.length === 0">
                                    <TableBodyTd colspan="3"> No hay Productos registrados.</TableBodyTd>
                                </TableBodyTr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
            <!-- Paginación -->
            <Pagination :links="productos.links" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            buscador: {
                search: this.filtro.search,
                orderBy: this.filtro.orderBy
            },

            // Verificador
            editableId: null,
            editableCelda: null,

            productSelected: {},

            // Formulario
            form: this.$inertia.form({
                id_producto: '',
                campo: '',
                valor: '',
            }),
        }
    },

    watch: {
        buscador: {
            deep: true,
            handler: function () {
                setTimeout(() => {
                    this.getData();
                    // router.get(`/products`, {search: this.buscador.search }, { preserveState: true });
                }, 150)
            }
        }
    },

    methods: {
        cleanEditables() {
            this.editableId = null;
            this.editableCelda = null;
        },

        cleanForm() {
            this.form.campo = '';
            this.form.id_producto = '';
            this.form.valor = '';
        },

        editPlus(plusEditar, index) {
            if (plusEditar === null || plusEditar === '' || Number(plusEditar) <= 0) {
                this.productos.data[index].plus = this.productSelected.plus;
                this.cleanEditables();
                this.cleanForm();
                return;
            }

            if (plusEditar !== this.productSelected.plus) {
                this.form.campo = 'plus';
                this.form.id_producto = this.productSelected.id_producto
                this.form.valor = plusEditar;
                this.editData().then(() => {
                    this.cleanEditables();
                    this.cleanForm();
                });
            } else {
                this.cleanEditables();
                this.cleanForm();
            }
        },

        editNombre(nombre) {
            if (nombre !== this.productSelected.nombre) {
                this.form.campo = 'nombre';
                this.form.id_producto = this.productSelected.id_producto
                this.form.valor = nombre;
                this.editData().then(() => {
                    this.cleanEditables();
                    this.cleanForm();
                });
            } else {
                this.cleanEditables();
                this.cleanForm();
            }
            
        },

        enableEditable(producto, celda) {
            this.editableId = producto.id_producto;
            this.editableCelda = celda;
            this.productSelected = { ...producto };
        },

        async editData() {
            try {
                await Promise.resolve(this.form.put('/productEdit'));
            } catch (error) {
                console.error("Error actualizando datos:", error);
            }
            
        },

        activate(producto) {
            this.form.id_producto = producto.id_producto
            if (confirm('¿Esta seguro que quiere dar de alta a ' + producto.nombre + '?')) {
                this.form.put('/productActivate');
                console.log("producto Activado");
            }
            /*
            this.form.put('/productActivate');
            */
        },

        deactivate(producto) {
            this.form.id_producto = producto.id_producto
            if (confirm('¿Está seguro que quiere dar de baja a ' + producto.nombre + '?' )) {
                this.form.put('/productDelete');
                console.log("producto desactivado");
            }
            /*
            this.form.put('/productDelete');
            */
        },

        ordenarPor(value) {
            this.buscador.orderBy = value
            this.getData();
        },

        getData() {
            router.get(`/products`, {search: this.buscador }, { preserveState: true });
        }
    },
}
</script>
