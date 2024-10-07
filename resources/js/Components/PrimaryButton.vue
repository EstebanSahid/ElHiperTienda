<template>
    <button
        class="button-hiper inline-flex items-center 
        rounded-md border border-transparent 
        px-4 py-2 text-xs font-semibold 
        uppercase tracking-widest text-white 
        transition duration-150 ease-in-out hover:bg-gray-700
        focus:bg-gray-700 focus:outline-none focus:ring-2 
        focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900
        dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white 
        dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
    >
        <slot />
    </button>
</template>

<style lang="css">
    .button-hiper {
        background-color: #97a907
    }

    .button-hiper:hover {
        background-color: #59650f;
    }
</style>
