<template>
    <div class="box" @click.stop="toggle()" :class="{'active': value_}">
        <transition name="slide">
            <div class="checkmark" v-show="value_">&#61740;</div>
        </transition>
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
    .box
        height: 18px
        width: 18px
        border: 2px solid #666
        border-radius: 4px
        position: relative
        margin: 11px
        cursor: pointer
        user-select: none
        vertical-align: top

        &.active
            background: var(--primary)
            border-color: var(--primary)

            .checkmark
                color: white

        .checkmark
            height: 18px
            width: 18px
            position: absolute
            top: -2px
            left: -2.1px
            line-height: 18px
            text-align: center
            color: #666
            font-family: 'Material Icons'
            font-size: 15px
            transition: clip-path 100ms 60ms cubic-bezier(0.19, 0.01, 1, 0.27)
            clip-path: inset(0 0 0 0)

        .slide-enter
            clip-path: inset(0 100% 0 0)
            
        .slide-leave-to
            clip-path: inset(0 0 0 100%)
</style>