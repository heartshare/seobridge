<template>
    <div class="ccc-container" @click.stop="toggle()">
        <div class="box" :class="{'active': value_}">
            <transition name="slide">
                <div class="checkmark" v-show="value_">&#983340;</div>
            </transition>
            <input class="input" type="checkbox" :name="name" :value="value_">
        </div>

        <div class="label" v-if="this.$slots.default">
            <slot></slot>
        </div>

        <div class="border" v-if="hasBorder && this.$slots.default"></div>
    </div>
</template>

<script>
    export default {
        props: {
            value: Boolean,
            name: String,
            label: String,
            noBorder: {},
        },
        data() {
            return {
                value_: false,
                hasBorder: true,
            }
        },
        mounted() {
            if (typeof this.noBorder !== 'undefined') this.hasBorder = false
            
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
    .ccc-container
        position: relative
        vertical-align: top
        display: inline-flex
        gap: 11px
        padding: 11px
        cursor: pointer

        .box
            height: 18px
            width: 18px
            border: 2px solid #666
            border-radius: 4px
            position: relative
            user-select: none
            

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

            .input
                display: none
        
        .label
            font-size: var(--button-size)
            line-height: 18px
            user-select: none

        .border
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 5px
            border: var(--input-border)
            pointer-events: none
</style>