<template>
    <div class="ccc-ui-container" @click="$emit('click')" v-close-popover>
        <div class="icon" v-if="icon_" v-html="icon_"></div>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: ['icon'],

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

        &:hover
            background: var(--primary-shade)
            color: var(--primary)

            .icon
                color: inherit

        .icon
            color: inherit
            font-size: 20px !important
            color: var(--text-gray)
            letter-spacing: 0
            font-weight: normal
            pointer-events: none
            vertical-align: top
            font-family: 'Material Icons'
</style>