<template>
    <div class="container" @click.stop="toggle()" :class="{'active': value_}">
        <div class="knob"></div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Boolean
            }
        },
        data() {
            return {
                value_: false,
            }
        },
        mounted() {
            if( this.value === true || this.value === false )
            {
                this.value_ = this.value
            }
        },
        watch: {
            value() {
                if( this.value === true || this.value === false )
                {
                    this.value_ = this.value
                }
            }
        },
        methods: {
            toggle() {
                this.value_ = !this.value_
                this.$emit('input', this.value_)
            },
        }
    }
</script>

<style lang="sass" scoped>
    .container
        height: 22px
        width: 40px
        background: var(--text-gray)
        border-radius: 30px
        position: relative
        margin: 9px 10px
        cursor: pointer
        user-select: none
        vertical-align: top

        &.active
            background: var(--success)

            .knob
                transform: translateX(18px)

        .knob
            height: 16px
            width: 16px
            border-radius: 10px
            background: var(--bg)
            position: absolute
            top: 3px
            left: 3px
            will-change: transform
            transition: all 100ms ease-out
</style>