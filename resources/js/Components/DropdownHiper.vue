<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white dark:bg-gray-700',
    },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        48: 'w-48',
        40: 'w-40',
        32: 'w-32',
    }[props.width.toString()] ?? 'w-48';
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else {
        return 'origin-top';
    }
});
</script>

<template>
    <div class="relative">
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
            @click="open = !open"
        >
            <slot name="trigger" />
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

        <!-- Fondo oscuro para cerrar -->
        <div
            v-show="open"
            class="fixed inset-0 z-50"
            @click="open = false"
        ></div>

        <!-- Menú -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-[9999999] mt-2 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                    @click="open = false"
                >
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>
