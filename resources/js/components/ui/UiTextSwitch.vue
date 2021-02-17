<template>
    <div class="ui-container">
        <div class="option" v-for="(option, i) in options_" :key="i" :class="{'selected': i === value_}" @click="select(i)">{{option}}</div>

        <div class="border"></div>
    </div>
</template>

<script>
    export default {
        props: {
            options: Object,
            value: String,
        },

        data() {
            return {
                value_: 'ON',
                options_: {ON: 'on', OFF: 'off'},
            }
        },

        mounted() {
            this.options_ = this.options ? this.options : {ON: 'on', OFF: 'off'}
            this.value_ = this.options_.hasOwnProperty(this.value) ? this.value : Object.keys(this.options_)[0]
        },

        watch: {
            value() {
                this.value_ = this.options_.hasOwnProperty(this.value) ? this.value : Object.keys(this.options_)[0]
            }
        },

        methods: {
            select(selection) {
                if (this.options_.hasOwnProperty(selection))
                {
                    this.value_ = selection
                }

                this.$emit('input', this.value_)
            }
        }
    }
</script>

<style lang="sass" scoped>
    .ui-container
        --height: 30px
        height: var(--height)
        line-height: var(--height)
        border-radius: var(--height)
        position: relative
        user-select: none

        .border
            position: absolute
            top: 0
            left: 0
            border: var(--input-border)
            border-color: var(--secondary)
            height: 100%
            width: 100%
            border-radius: var(--height)
            pointer-events: none

        .option
            height: 100%
            min-width: 70px
            line-height: 24px
            font-size: 12px
            padding: 5px 15px
            text-align: center
            letter-spacing: 1px
            font-family: rubik
            text-transform: uppercase
            border-radius: var(--height)
            color: var(--secondary)
            position: relative
            cursor: pointer
            transition: all 120ms

            &:hover
                background: var(--secondary-shade)
                color: var(--secondary)

            &.selected
                background: var(--secondary)
                color: white
                z-index: 1
</style>