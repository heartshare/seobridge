<template>
    <div class="input-container" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}, {'has-stepper': stepper}]">
        <input class="input" ref="input" :min="min" :max="max" :step="step" :autocomplete="ac" :name="name" v-model="value_" type="number" @input="emit()" @focus="isFocused = true" @blur="isFocused = false" spellcheck="false">
        
        <div class="border" v-if="hasBorder"></div>

        <div v-if="isStepper" @click="stepUp()" class="step up">&#62485;</div>
        <div v-if="isStepper" @click="stepDown()" class="step down">&#62324;</div>

        <div class="label">{{label}}</div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {},
            label: String,
            max: {
                type: Number,
                default: Infinity,
            },
            min: {
                type: Number,
                default: -Infinity,
            },
            noBorder: {},
            name: String,
            step: {
                type: Number,
                default: 1,
            },
            stepper: String,
            ac: String,
        },

        data() {
            return {
                value_: null,
                score: 0,
                errors: {},
                hasBorder: true,
                isInvalid: false,
                isVisible: false,
                isStepper: false,
                isFocused: false,
            }
        },

        mounted() {
            if (typeof this.stepper !== 'undefined') this.isStepper = true
            if (typeof this.noBorder !== 'undefined') this.hasBorder = false

            this.value_ = !isNaN(parseFloat(this.value)) ? (this.value) : null
            this.validate()
        },

        watch: {
            value() {
                this.value_ = !isNaN(parseFloat(this.value)) ? (this.value) : null
                this.validate()
            },
        },

        computed: {
            isFocusedOrFilled() {
                return (!isNaN(parseFloat(this.value_)) || this.isFocused)
            }
        },

        methods: {
            stepUp(step = this.step) {
                if (this.value_ == '') this.value_ = 0
                if (this.value_ + this.step <= this.max) this.value_ += step
                this.$refs.input.focus()
                this.emit()
            },

            stepDown(step = this.step) {
                if (this.value_ == '') this.value_ = 0
                if (this.value_ - this.step >= this.min) this.value_ -= step
                this.$refs.input.focus()
                this.emit()
            },

            emit(value = this.value_) {
                this.$emit('input', !isNaN(parseFloat(value)) ? parseFloat(value) : null)
                this.validate()
            },

            validate() {
                if (this.max < this.value_)
                {
                    this.errors.max = false
                }
                else
                {
                    delete this.errors.max
                }

                if (this.min > this.value_)
                {
                    this.errors.min = false
                }
                else
                {
                    delete this.errors.min
                }

                this.isInvalid = (Object.keys(this.errors).length > 0)
            },
        },
    }
</script>

<style lang="sass" scoped>
    .input-container
        --height: 50px

        height: var(--height)
        width: 100%
        background: var(--bg)
        border-radius: 5px
        position: relative
        text-align: left

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
                padding-top: 15px !important

        &.has-stepper
            .input
                padding: 0 calc(var(--height) / 2 + 5px) 0 15px !important

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
            padding: 0 calc(var(--height) / 2 + 5px) 0 15px
            white-space: nowrap
            overflow: hidden
            text-overflow: ellipsis
            text-align: left
            pointer-events: none
            transition: all 200ms
            color: var(--text-gray)
            transform-origin: top left

        .step
            width: calc(var(--height) / 2 - 5px)
            height: calc(var(--height) / 2 - 5px)
            line-height: calc(var(--height) / 2 - 5px)
            text-align: center
            font-size: 16px
            font-family: 'Material Icons'
            border-radius: 100%
            cursor: pointer
            user-select: none
            position: absolute
            right: 5px

            &:hover
                background: var(--primary-shade)
                color: var(--primary)

            &.up
                top: 5px
            &.down
                bottom: 5px

        .input
            height: 100%
            width: 100%
            padding: 0 15px
            border: none
            background: none
            border-radius: 3px
            font-family: var(--text-font)
            font-size: 16px
            text-align: inherit
            color: var(--heading-gray)
            -moz-appearance: textfield

            &::-webkit-outer-spin-button,
            &::-webkit-inner-spin-button
                -webkit-appearance: none
                margin: 0
</style>