<template>
    <button class="ccc-ui-container" @click="$emit('click')" :disabled="disabled_" v-close-popover>
        <div class="icon" v-if="icon_" v-html="icon_"></div>
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: ['icon', 'disabled'],

        data() {
            return {
                icon_: null,
            }
        },

        mounted() {
            this.init()
        },

        watch: {
            icon() {
                this.init()
            },
        },

        computed: {
            disabled_(){
                return (this.disabled == true || (typeof this.disabled !== 'undefined' && this.disabled === ''))
            },
        },

        methods: {
            init()
            {
                if (this.icon)
                {
                    this.icon_ = this.icon
                }
            },
        }
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        display: block
        font-size: var(--text-size)
        color: var(--heading-gray)
        padding: 0 15px
        height: 46px
        cursor: pointer
        user-select: none
        display: flex
        align-items: center
        gap: 15px
        background: transparent
        border: none
        width: 100%
        font-family: var(--text-font)

        .icon
            color: inherit
            font-size: 20px !important
            color: var(--text-gray)
            letter-spacing: 0
            font-weight: normal
            pointer-events: none
            vertical-align: top
            font-family: 'Material Icons'

        &:hover
            background: var(--primary-shade)
            color: var(--primary)

            .icon
                color: inherit

        &:disabled
            color: var(--disabled-color)
            background: transparent
            cursor: auto

            .icon
                color: inherit
</style>