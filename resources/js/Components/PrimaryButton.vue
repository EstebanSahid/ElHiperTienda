<template>
    <button
        class="p-2 button-hiper inline-flex items-center 
        rounded-md border border-transparent 
        bg-[#97a907]
        px-4 py-2 text-xs font-semibold 
        uppercase tracking-widest text-white 
        hover:bg-[#59650f]
        transition duration-150 ease-in-out
        focus:bg-[#59650f] focus:outline-none focus:ring-2 
        focus:ring-[#59650f] focus:ring-offset-2 
        dark:focus:bg-[#97a907]
        dark:bg-[#71800b] dark:hover:bg-[#97a907]"
    >
        <slot />
    </button>   
</template>
