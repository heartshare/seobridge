<template>
    <label class="input-container" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}, {'spacer': displayMax}]">
        <textarea class="input" :autocomplete="ac" :name="name" v-model="value_" @input="emit()" @focus="isFocused = true" @blur="isFocused = false" spellcheck="false"></textarea>
        
        <div class="border"></div>

        <div class="label">{{label}}</div>

        <div class="chars" v-if="displayMax">{{value_.length}} / {{max}}</div>
    </label>
</template>

<script>
    export default {
        props: {
            value: String,
            label: String,
            max: {},
            showMax: {},
            name: String,
            ac: String,
        },

        data() {
            return {
                value_: '',
                score: 0,
                errors: {},
                isInvalid: false,
                isVisible: false,
                isFocused: false,
                displayMax: false,
            }
        },

        mounted() {
            this.value_ = (typeof this.value === 'string') ? this.value : ''
            if (typeof this.showMax !== 'undefined' && this.max !== 'undefined') this.displayMax = true
            this.validate()
        },

        watch: {
            value() {
                this.value_ = (typeof this.value === 'string') ? this.value : ''
                this.validate()
            },
        },

        computed: {
            isFocusedOrFilled() {
                return (this.value_.length > 0 || this.isFocused)
            }
        },

        methods: {
            emit() {
                this.$emit('input', this.value_)
                this.validate()
            },

            validate() {
                if (this.max < this.value_.length)
                {
                    this.errors.maxLength = false
                }
                else
                {
                    delete this.errors.maxLength
                }

                this.isInvalid = (Object.keys(this.errors).length > 0)
            },
        },
    }
</script>

<style lang="sass" scoped>
    .input-container
        --height: 50px

        min-height: var(--height)
        width: 100%
        background: white
        border-radius: 5px
        position: relative
        resize: vertical

        &.focused
            .border
                border-color: #666

        &.focused-or-filled
            .progress-bar
                transform: scaleY(1)

            .label
                transform: translate(4px, -5px) scale(0.72)

        &.has-label
            .input
                margin-top: 22px !important
                height: calc(100% - 22px)
                max-height: calc(100% - 22px)

        &.spacer
            margin-bottom: 15px

        &.invalid
            .border
                border-color: var(--error)
            .chars
                color: var(--error)

        .border
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 5px
            border: var(--input-border)
            pointer-events: none

        .label
            font-size: 16px
            height: var(--height)
            width: 100%
            line-height: calc(var(--height) + 3px)
            position: absolute
            top: 0
            left: 0
            padding: 0 15px
            white-space: nowrap
            overflow: hidden
            text-overflow: ellipsis
            text-align: left
            pointer-events: none
            transition: all 200ms
            color: #00000099
            transform-origin: top left

        .chars
            font-size: 10px
            max-width: 100%
            line-height: 16px
            position: absolute
            right: 11px
            bottom: -16px
            text-align: right
            white-space: nowrap
            overflow: hidden
            text-overflow: ellipsis
            pointer-events: none
            color: #00000099

        .input
            min-height: inherit
            height: calc(100% - 15px)
            max-height: calc(100% - 15px)
            width: 100%
            padding: 0 15px
            margin-top: 15px
            border: none
            background: none
            border-radius: 3px
            font-family: rubik
            font-size: 16px
            resize: inherit
</style>