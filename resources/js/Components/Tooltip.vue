<template>
    <div class="static" @mouseenter="startShow" @mouseleave="hide">
        <slot></slot>
        <div v-if="visible" class="absolute grid justify-items-center bg-gray-700 text-white text-[10px] py-1 px-2 rounded">
            {{ text }}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            text: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                visible: false,
                timeout: null
            };
        },
        
        methods: {
            startShow() {
                setTimeout(() => {
                    this.show();
                }, 1500);
            },
            show() {
                clearTimeout(this.timer);
                this.visible = true;
            },
            hide() {
                clearTimeout(this.timeout);
                this.visible = false;
            }
        },
    };
</script>