<template>
    <div class="fixed right-0 bottom-0 p-4 items-center z-50 min-w-max transition-transform duration-3000 ease-in-out translate-x-0">
        <!-- Notificacion de error de Login Reset -->
        <div v-if="$page.props.flash.notFound && show" 
            class="col-start-2 col-span-4 flex items-center justify-between my-4 max-w-3xl bg-[#ff0606] dark:bg-[#7C0000] rounded">
            <div class="flex items-center">
                <svg class="shrink-0 ml-4 mr-2 w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
                <div class="py-4 text-white text-sm font-medium">{{ $page.props.flash.notFound }}</div>
            </div>
            <button type="button" class="group mr-2 p-2" @click="show = false">
                <svg class="block w-2 h-2 fill-red-800 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
            </button>
        </div>

        <!-- Notificacion de error de Login Reset -->
        <div v-if="$page.props.flash.CredencialesIncorrectas && show" 
            class="col-start-2 col-span-4 flex items-center justify-between my-4 max-w-3xl bg-[#ff0606] dark:bg-[#7C0000] rounded">
            <div class="flex items-center">
                <svg class="shrink-0 ml-4 mr-2 w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
                <div class="py-4 text-white text-sm font-medium">{{ $page.props.flash.CredencialesIncorrectas }}</div>
            </div>
            <button type="button" class="group mr-2 p-2" @click="show = false">
                <svg class="block w-2 h-2 fill-red-800 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
            </button>
        </div>

        <!-- Notificacion de registro exitoso -->
        <div v-if="$page.props.flash.success && show" class=" col-start-2 col-span-4 flex items-center justify-between my-4 max-w-3xl dark:bg-[#71800b] bg-[#97a907] rounded">
            <div class="flex items-center">
                <svg class="shrink-0 ml-4 mr-2 w-4 h-4 dark:fill-white fill-[rgb(75,86,17)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><polygon points="0 11 2 9 7 14 18 3 20 5 7 18" /></svg>
                <div class="py-4 dark:text-white text-[#4b5611] text-sm font-medium">{{ $page.props.flash.success }}</div>
            </div>
            <button type="button" class="group mr-2 p-2" @click="show = false">
                <svg class="block w-2 h-2 fill-green-800 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
            </button>
        </div>

        <!-- Notificacion de error de registro-->
        <div v-if="($page.props.flash.error || Object.keys($page.props.errors).length > 0) && show" 
            class="col-start-2 col-span-4 flex items-center justify-between my-4 max-w-3xl bg-[#ff0606] dark:bg-[#7C0000] rounded">
            <div class="flex items-center">
                <svg class="shrink-0 ml-4 mr-2 w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
                <div v-if="$page.props.flash.error" class="py-4 text-white text-sm font-medium">{{ $page.props.flash.error }}</div>
                <div v-else class="py-4 text-white text-sm font-medium">
                    <span v-if="Object.keys($page.props.errors).length === 1">Hubo un inconveniente</span>
                    <span v-else>Hubieron {{ Object.keys($page.props.errors).length }} inconvenientes.</span>
                </div>
            </div>
            <button type="button" class="group mr-2 p-2" @click="show = false">
                <svg class="block w-2 h-2 fill-red-800 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
            </button>
        </div>

        <!-- Notificaciones del Catch -->
        <div v-if="$page.props.error && show" 
            class="col-start-2 col-span-4 flex items-center justify-between my-4 max-w-3xl bg-[#ff0606] dark:bg-[#7C0000] rounded">
            <div class="flex items-center">
                <svg class="shrink-0 ml-4 mr-2 w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
                <div class="py-4 text-white text-sm font-medium">{{ $page.props.error.mensaje }}</div>
            </div>
            <button type="button" class="group mr-2 p-2" @click="show = false">
                <svg class="block w-2 h-2 fill-red-800 group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            show: true,
            multiplicadorSegundos: 1000,
        }
    },

    mounted() {
        // Cuando el componente esté montado, verifica si hay un mensaje flash
        const flash = this.$page.props.flash || {};
        const error = this.$page.props.error || flash.error || null;
        const success = flash.success || null;
        const duracionNotificacion = error && error.duracionNotificacion ? error.duracionNotificacion : null;

        if (success || error) {
            const duracion = this.obteneregundos(duracionNotificacion);
            this.showNotification(duracion);
        }
    },
    watch: {
        '$page.props.flash': {
            handler(newValue) {
                // Solo muestra la notificación si hay un nuevo mensaje flash
                if (newValue?.success || newValue?.error || newValue?.notFound || newValue?.CredencialesIncorrectas) {
                    const duracion = this.obteneregundos(newValue.duracionNotificacion);
                    this.showNotification(duracion);
                }
            },
            deep: true,
        },

        '$page.props.error': {
            handler(newValue) {
                // Solo muestra la notificación si hay un nuevo mensaje de error
                if (newValue?.success || newValue?.error || newValue?.notFound || newValue?.CredencialesIncorrectas) {
                    const duracion = this.obteneregundos(newValue.duracionNotificacion);
                    this.showNotification(duracion);
                }
            },
            deep: true,
        },
    },
    methods: {
        showNotification(duracion) {
            this.show = true;

            setTimeout(() => {
                this.show = false;
            }, duracion);
        },

        obteneregundos(segundos) {
            const duracion = segundos || 4; // Valor predeterminado: 4 segundos
            return duracion * this.multiplicadorSegundos;
        },
    },
}
</script>