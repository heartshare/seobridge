<template>
    <div class="container">
        <div class="button" @click="click()" :class="[{'disabled': disabled}, {'loading': loading}]">
            {{loading ? 'Waiting...' : 'Donate Now'}}
            <ui-spinner color="white" size="20" class="spinner"></ui-spinner>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            disabled: {
                type: Boolean
            },

            loading: {
                type: Boolean
            },
        },

        methods: {
            click() {
                if (!this.disabled && !this.loading)
                {
                    this.$emit('click')
                }
            }
        }
    }
</script>

<style lang="sass" scoped>
    .container
        position: relative
        width: 100%

        .button
            height: 40px
            line-height: 43px
            width: 100%
            border: 0px
            text-align: center
            text-decoration: none
            text-transform: uppercase
            color: white
            border-radius: 5px
            font-family: rubik
            font-size: 14px
            font-weight: 500
            letter-spacing: 1px
            padding: 0 20px
            background: #2161ff
            user-select: none
            cursor: pointer
            vertical-align: top
            transition: all 100ms

            &:not(.disabled):hover
                filter: drop-shadow(0 3px 4px #00000040)

            &.disabled
                filter: none
                background: #888
                pointer-events: none

            &.loading
                filter: none
                padding-right: 45px
                background: #888
                pointer-events: none
                text-transform: none

                .spinner
                    opacity: 1

            .spinner
                position: absolute
                top: 0
                right: 0
                margin: 10px
                pointer-events: none
                opacity: 0
                transition: opacity 100ms
</style>