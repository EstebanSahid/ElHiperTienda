<script setup>

const model = defineModel({
    type: String,
    required: true,
});

</script>

<template>
    <select
        class="input-hiper rounded-md border-gray-300 shadow-sm focus:border-indigo-500 
        focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
        dark:focus:border-indigo-600 dark:focus:ring-indigo-600 text-sm"
        v-model="model"
    >
        <slot />
    </select>
</template>