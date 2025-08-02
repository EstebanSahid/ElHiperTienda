<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Table from '@/Components/Table.vue';
import TableTh from '@/Components/TableTh.vue';
import TableBodyTr from '@/Components/TableBodyTr.vue';
import TableBodyTd from '@/Components/TableBodyTd.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { dameFechaFormateada } from '@/Services/DateHelper';
</script>

<template>
    <Head title="Historial" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-md font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Pedido de {{ pedido.tienda }} del {{ dameFechaFormateada(pedido.fecha) }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Aqui los Reportes por filtro -->
                        <div class="grid grid-cols-2 md:grid-cols-4">
                            <TextInput 
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="buscadorText"
                                autofocus
                                autocomplete="name"
                                placeholder="Buscar por nombre o plus"
                            />
                        </div>

                        <!-- Aqui la Tabla de Datos -->
                        <div class="py-3">
                            <div class="bg-white rounded-md shadow overflow-x-auto dark:bg-gray-800">
                                <Table>
                                    <thead>
                                        <TableTh @click="ordenar('plus_producto')">Plus 
                                            <span v-if="sortBy === 'plus_producto'">
                                                {{ sortAsc ? '↑' : '↓' }}
                                            </span>
                                        </TableTh>
                                        <TableTh @click="ordenar('nombre_producto')">Producto 
                                            <span v-if="sortBy === 'nombre_producto'">
                                                {{ sortAsc ? '↑' : '↓' }}
                                            </span>
                                        </TableTh>
                                        <TableTh @click="ordenar('cantidad')">Cantidad 
                                            <span v-if="sortBy === 'cantidad'">
                                                {{ sortAsc ? '↑' : '↓' }}
                                            </span>
                                        </TableTh>
                                        <TableTh @click="ordenar('unidadMedida')">Unidad de Medida 
                                            <span v-if="sortBy === 'unidadMedida'">
                                                {{ sortAsc ? '↑' : '↓' }}
                                            </span>
                                        </TableTh>
                                    </thead>
                                    <TableBodyTr
                                        v-for="producto in productosFiltrados" :key="producto.id_pdetalle" :value="producto.id_pdetalle"
                                    >
                                        <TableBodyTd> {{ producto.plus_producto }}</TableBodyTd>
                                        <TableBodyTd> {{ producto.nombre_producto }}</TableBodyTd>
                                        <TableBodyTd> {{ producto.cantidad }}</TableBodyTd>
                                        <TableBodyTd> {{ producto.unidadMedida }}</TableBodyTd>
                                    </TableBodyTr>
                                    
                                    <TableBodyTr v-if="productos.length === 0">
                                        <TableBodyTd colspan="4" class="font-semibold" >Sin Registros</TableBodyTd>
                                    </TableBodyTr>
                                </Table>
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
        pedido: Object,
    },

    data() {
        return {
            buscadorText: '',
            sortBy: 'nombre_producto', // default
            sortAsc: true,
            productosOriginales: this.productos, // viene del backend
        }
    },
    
    mounted() {
        console.log("mounted")
        console.log(this.productos);
    },

    computed: {
        productosFiltrados() {
            let resultado = [...this.productosOriginales];

            // Filtramos por nombre o plus
            if (this.buscadorText) {
                const texto = this.buscadorText.toLowerCase();
                resultado = resultado.filter(orden =>
                    producto.nombre_producto.toLowerCase().includes(texto) ||
                    producto.plus_producto.toLowerCase().includes(texto)
                )
            }

            // Ordenamos
            resultado.sort((a, b) => {
                const valA = a[this.sortBy]?.toString().toLowerCase();
                const valB = b[this.sortBy]?.toString().toLowerCase();
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            }); 

            return resultado;

        }
    },

    methods: {
        // Generar el PDF
        generarPDF() {
            const dataToSend = { 
                pedidos: this.pedidos,
                tiendas: this.tiendas,
                dataThead: this.dataThead,
                fecha: this.buscador.fecha,
                numerosPedido: this.numerosPedido,
            };

            axios.post('/generatePDF', dataToSend, { responseType: 'blob' })
            .then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', `Reporte-${this.buscador.fecha}.pdf`);
                document.body.appendChild(link);
                link.click();
            })
            .catch(error => {
                console.error('Error generando el PDF:', error);
            });
        },

        ordenar(campo) {
            if (this.sortBy === campo) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortBy = campo;
                this.sortAsc = true;
            }
        }
    },
}
</script>
