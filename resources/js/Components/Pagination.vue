<template>
    <div v-if="links.length > 3">
        <!--
        <div class="flex justify-center flex-col md:flex-row my-6">
        -->
        <div class="my-6 grid grid-cols-3 gap-2 md:flex md:flex-row md:justify-center">
            <template v-for="(link, key) in links">
                <div 
                    v-if="link.url === null" :key="key" 
                    class="mb-1 mr-1 px-4 py-3 dark:text-gray-700 text-gray-400 text-sm leading-4 border rounded" v-html="link.label" 
                />
                <Link 
                    v-else :key="`link-${key}`" 
                    class="
                    mb-1 mr-1 px-4 py-3 
                    rounded border 
                    dark:text-white dark:hover:bg-gray-800
                    focus:text-indigo-500 text-sm leading-4 hover:bg-white
                    focus:border-indigo-500" 
                    :class="{ 'bg-white dark:bg-gray-800': link.active }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link,
    },
    props: {
        links: Array,
    },

    mounted(){
    }
}
</script>