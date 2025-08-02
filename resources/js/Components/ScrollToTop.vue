<template>
    <div v-if="enableScrollButton" class="fixed bottom-6 left-1/2 z-10 transition-transform duration-200 ease-in-out hover:scale-110">
            <button 
                type="button" 
                class="group flex items-center justify-center border-2 w-12 h-12 rounded-full hover:shadow-lg
                bg-white border-gray-400 hover:border-gray-400
                dark:bg-gray-700 dark:border-gray-900 dark:hover:border-gray-900"
                @click="scrollTop"
            >
                <svg class="block w-6 h-6 fill-gray-400 group-hover:fill-gray-400 dark:fill-gray-900 dark:group-hover:fill-gray-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M4 12l1.41 1.41L11 7.83v12.34h2V7.83l5.59 5.58L20 12l-8-8z" />
                </svg>
            </button>
        </div>
</template>

<script>
export default {
    data() {
        return {
            enableScrollButton: false,
        }
    },

    methods: {
        // Monitorear scroll
        handleScroll() {
            this.enableScrollButton = window.scrollY > 400
        },

        // Regresar scroll
        scrollTop() {
            window.scrollTo({
                top: 180,
                behavior: 'smooth'
            });

            this.enableScrollButton = false;
        },
    },

    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeDestroy() {
        window.removeEventListener('scroll', this.handleScroll);
    },
}
</script>