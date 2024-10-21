<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; 
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Tooltip from '@/Components/Tooltip.vue';
</script>

<template>
    <Head title="Nueva Orden" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Generar orden para {{ tienda[0].nombre }}
                </h2>

                <PrimaryButton @click="validarOrden(productosOrden)">Guardar</PrimaryButton>

            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100 content-end">
                        <div class="grid grid-cols-1 md:grid-cols-2">
                            <!-- CONTENEDOR DE PRODUCTOS -->
                            <div>
                                <div class="px-10 py-1">
                                    <!-- Cabecera -->
                                    <div class="p-3 font-semibold">
                                        Lista de Productos Disponibles
                                    </div>
                                    <!-- Buscador -->
                                    <div class="p-3">
                                        <InputLabel for="search" value="Buscar Producto" />

                                        <TextInput
                                            id="search"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="buscador.search"
                                            required
                                        />
                                    </div>
                                    <!-- Tabla -->
                                    <div class="py-3">
                                        <!-- Table Index -->
                                        <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                                            <Table>
                                                <thead>
                                                    <tr class="text-center font-bold">
                                                        <TableTh>PLUS</TableTh>
                                                        <TableTh>Nombre</TableTh>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <TableBodyTr v-for="producto in productos.data" :key="producto.id_producto">
                                                        <TableBodyTd @click="agregarProducto(producto)" >{{ producto.plus }}</TableBodyTd>
                                                        <TableBodyTd @click="agregarProducto(producto)" >{{ producto.nombre }}</TableBodyTd>
                                                    </TableBodyTr>
                                                    
                                                    <TableBodyTr v-if="productos.links.length > 3">
                                                        <TableBodyTd colspan="2" class="font-semibold" >Para mostrar mas productos, por favor utilice el buscador o digite el código.</TableBodyTd>
                                                    </TableBodyTr>
                                                    
                                                    <TableBodyTr v-if="productos.data.length === 0">
                                                        <TableBodyTd colspan="2" class="font-semibold" >No se encontro el producto.</TableBodyTd>
                                                    </TableBodyTr>
                                                    
                                                </tbody>
                                            
                                            </Table>
                                        </div>
                                        <!-- Paginación 
                                        <Pagination v-if="productos.links.length < 10" :links="productos.links" />
                                        -->
                                    </div>
                                </div>
                            </div>
                            <!-- CONTENEDOR DE PRODUCTOS PARA PEDIR -->
                            <div>
                                <div class="p-3 font-semibold">
                                    Lista de Productos a pedir
                                </div>
                                <div class="p-3">
                                    <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                                        <Table>
                                            <thead>
                                                <tr class="text-center font-bold">
                                                    <TableTh>PLUS</TableTh>
                                                    <TableTh>Nombre</TableTh>
                                                    <TableTh>Cantidad</TableTh>
                                                    <TableTh collapsan="2">
                                                        Unidad
                                                        <!--
                                                            -->
                                                        <span class="font-normal text-sm">( Click para cambiar de unidad )</span>
                                                    </TableTh>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <TableBodyTr
                                                    v-for="(producto, index) in productosOrden" :key="producto.id_producto"
                                                >
                                                    <TableBodyTd>{{ producto.plus }}</TableBodyTd>
                                                    <TableBodyTd>{{ producto.nombre }}</TableBodyTd>
                                                    <TableBodyTd>
                                                        <input 
                                                            @keyup="validarMayorCero(producto)"
                                                            min="1"
                                                            class="rounded-md w-4/5 border-gray-300
                                                            focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 
                                                            dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 
                                                            dark:focus:ring-indigo-600"
                                                            type="number" 
                                                            v-model="producto.cantidad"
                                                            placeholder="Cantidad"
                                                        />
                                                    </TableBodyTd>
                                                    <TableBodyTd>
                                                        <!--
                                                        <Tooltip text="Click para cambiar de Unidad">
                                                        -->
                                                            <div class="mt-1 block cursor-pointer w-full" @click="cambiarUnidad(producto)">
                                                                <span>{{ obtenerCodigoUnidad(producto.id_unidad) }}</span>
                                                            </div>
                                                        <!--
                                                        </Tooltip>
                                                        -->
                                                    </TableBodyTd>
                                                    <td class="px-2">
                                                        <button type="button" class="flex group justify-center items-center" @click="deleteProductArray(index)">
                                                            <svg class="block w-2 h-2 fill-[#ff1111] group-hover:fill-[#be0000] dark:fill-[#ff1111] dark:group-hover:fill-[#ff8b8b]" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
                                                        </button>
                                                    </td>
                                                </TableBodyTr>
    
                                                <TableBodyTr v-if="productosOrden.length === 0">
                                                    <TableBodyTd colspan="4" class="font-semibold" >No hay productos registrados a esta orden.</TableBodyTd>
                                                </TableBodyTr>
                                            </tbody>
                                        </Table>
                                    </div>
                                </div>
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
        productos: Array,
        filtro: Object,
        unidadMedida: Array,
        tienda: Object
    },
    
    data() {
        
        return {
            buscador: {
                search: this.filtro.search
            },

            form: this.$inertia.form({
                fecha: null,
                idTienda: null,
                pedido: [],
            }),

            productosOrden: [],
            fechaActual: new Date().toISOString().slice(0, 10)
        }
    },
    methods: {
        // Quitar un producto de la orden
        deleteProductArray(index){
            this.productosOrden.splice(index, 1);
        },

        // Agregar el producto al arreglo para la orden
        agregarProducto(producto) {
            const productoExistente = this.productosOrden.find(
                p => p.id_producto === producto.id_producto
            )

            if (!productoExistente) {
                producto.id_unidad = 1;
                producto.cantidad = null;
                this.productosOrden.push(producto);
            } else {
                alert('El producto ya esta registrado para una orden')
            }
        },

        // Validar que siempre se registre una cantidad al producto
        validarMayorCero(producto){
            if (producto.cantidad < 1) {
                producto.cantidad = null;
            }
        },

        // Click para cambiar de unidad
        cambiarUnidad(producto) {
            const indexActual = this.unidadMedida.findIndex(
                unidad => unidad.id_unidad_pedido === producto.id_unidad
            );
            
            const nuevoIndex = (indexActual + 1) % this.unidadMedida.length;
            producto.id_unidad = this.unidadMedida[nuevoIndex].id_unidad_pedido;
        },

        // Encontrar la unidad actual y retornar el código
        obtenerCodigoUnidad(idUnidad) {
            const unidad = this.unidadMedida.find(unidad => unidad.id_unidad_pedido === idUnidad);
            return unidad ? unidad.codigo : 'No hay unidades';
        },

        // Validar antes de guardar la Orden
        validarOrden(orden) {
            if (orden.length < 1) {
                alert('Nada para Guardar');
                return;
            }

            this.validarCantidadProductos(orden);
        },

        // Verificar que tengan cantidad antes de guardar
        validarCantidadProductos(orden) {
            let productosSinCantidad = [];
            orden.forEach(producto => {
                if (producto.cantidad === null) {
                    productosSinCantidad.push(producto.nombre);
                }
            });

            if (productosSinCantidad.length == 1) {
                alert('El producto ' + productosSinCantidad + ' no tiene una cantidad asignada');
                return;
            }

            if (productosSinCantidad.length > 1) {
                alert('Los siguientes productos no tienen cantidad asignada:\n' + productosSinCantidad.join(', '));
                return;
            }

            this.guardarOrden(orden);
        },

        guardarOrden(orden) {
            // Formateamos el objeto form y enviamos
            this.form.idTienda = this.tienda[0].id_tienda;
            this.form.fecha = this.fechaActual;
            this.form.pedido = orden;
            this.form.post('/orders');
        },
    },
    

    watch: {
        buscador: {
            deep: true,
            handler: function () {
                setTimeout(() => {
                    router.get(`/order/${this.tienda[0].id_tienda}/create`, {search: this.buscador.search }, { preserveState: true });
                }, 150);
            },
        },
    },
    
    mounted() {
        /*
        console.log(this.productos)
        console.log("id tienda")
        console.log(this.tienda);
        */
    }
}
</script>

