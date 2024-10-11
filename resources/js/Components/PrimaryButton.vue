<template>
    <button
        class="p-2 button-hiper inline-flex items-center 
        rounded-md border border-transparent 
        bg-[#97a907]
        px-4 py-2 text-xs font-semibold 
        uppercase tracking-widest text-white 
        hover:bg-[#59650f]
        transition duration-150 ease-in-out
        focus:bg-gray-700 focus:outline-none focus:ring-2 
        focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900
        dark:text-gray-800
        dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300
        dark:bg-[#97a907] dark:hover:bg-[#eef85e]"
    >
        <slot />
    </button>   
</template>
