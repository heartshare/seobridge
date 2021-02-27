<template>
    <div class="input-container" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}, {'spacer': displayMax}]">
        <input class="input" :autocomplete="ac" :name="name" v-model="value_" :type="isVisible ? 'text' : 'password'" @input="emit()" @focus="isFocused = true" @blur="isFocused = false" spellcheck="false">
        
        <div class="border"></div>

        <div class="label">{{label}}</div>

        <div class="chars" v-if="displayMax">{{value_.length}} / {{max}}</div>
        
        <div tabindex="0" class="visibility-toggle" :class="{'visible': isVisible}" @click.stop="isVisible = !isVisible">&#983560;</div>
        
        <div class="progress-bar" v-if="hasRating">
            <div class="progress" :class="'score-'+score"></div>
        </div>
    </div>
</template>

<script>
    let zxcvbn = require('zxcvbn')
    export default {
        props: {
            value: {
                type: String
            },
            label: {
                type: String
            },
            rating: {},
            max: {},
            showMax: {},
            name: {},
            ac: {
                type: String
            }
        },

        data() {
            return {
                value_: '',
                score: 0,
                errors: {},
                isInvalid: false,
                isVisible: false,
                isFocused: false,
                hasRating: false,
                displayMax: false,
            }
        },

        mounted() {
            this.value_ = (typeof this.value === 'string') ? this.value : ''
            if (typeof this.rating !== 'undefined') this.hasRating = true
            if (typeof this.showMax !== 'undefined' && this.max !== 'undefined') this.displayMax = true
            this.calcScore()
            this.validate()
        },

        watch: {
            value() {
                this.value_ = (typeof this.value === 'string') ? this.value : ''
                this.calcScore()
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
                this.calcScore()
                this.validate()
            },

            calcScore() {
                // Performance got bad at around 50 chars
                if (this.value_.length <= 50)
                {
                    this.score = zxcvbn(this.value_).score
                }
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

        height: var(--height)
        width: 100%
        background: white
        border-radius: 5px
        position: relative

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
            line-height: var(--height)
            position: absolute
            top: 0
            left: 0
            padding: 0 var(--height) 0 15px
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
            height: 100%
            width: 100%
            padding: 0 var(--height) 0 15px
            border: none
            background: none
            border-radius: 3px
            font-family: var(--text-font)
            font-size: 16px

        .progress-bar
            height: 3px
            border-radius: 4px 4px 0 0
            background: #00000010
            width: calc(100% - 30px)
            position: absolute
            bottom: 1px
            left: 15px
            pointer-events: none
            transform: scaleY(0)
            transform-origin: center bottom
            text-align: left
            transition: all 200ms
            overflow: hidden

            .progress
                height: 100%
                transition: all 300ms
                position: absolute
                top: 0
                left: 0

                &.score-0
                    width: 5%
                    background: #eb3b5a

                &.score-1
                    width: 25%
                    background: #ee5253

                &.score-2
                    width: 50%
                    background: #ff9f43

                &.score-3
                    width: 75%
                    background: #f1c40f

                &.score-4
                    width: 100%
                    background: #2ecc71

        .visibility-toggle
            height: var(--height)
            width: var(--height)
            line-height: calc(var(--height) - 2px)
            text-align: center
            font-family: 'Material Icons'
            font-size: 22px
            color: var(--text-gray)
            background: transparent
            border: 1px solid transparent
            border-radius: 5px
            position: absolute
            top: 0
            right: 0
            z-index: 1
            cursor: pointer
            user-select: none
            transition: all 100ms

            &:focus
                border-color: #666

            &.visible
                color: var(--primary)

            &:after
                content: ''
                height: 0
                width: 0
                background: white
                border-left: 1.7px solid currentcolor
                border-right: 1.7px solid white
                position: absolute
                top: 16.6px
                left: 16px
                transform: rotate(-45deg)
                transform-origin: top center
                transition: height 200ms cubic-bezier(0.22, 0.61, 0.36, 1)

            &.visible:after
                height: 20px
</style>