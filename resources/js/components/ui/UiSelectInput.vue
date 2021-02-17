<template>
    <div class="ui-container" tabindex="0" @click.stop="isDropDownOpen = !isDropDownOpen" @focus="isFocused = true" @blur="isFocused = false; isDropDownOpen = false" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}]">        
        <input class="input" disabled :name="name" :value="value_.label" type="text" spellcheck="false">
        
        <div class="border" v-if="hasBorder"></div>

        <div class="label">{{label}}</div>

        <div class="dropdown" :class="{'open': isDropDownOpen}" @click.stop>
            <div class="item" v-for="(item, i) in options_" :key="i" @click="select(item.value)" :class="{'selected': value_.value == item.value}">{{item.label}}</div>
        </div>
        
        <div class="dropdown-toggle">&#61760;</div>
    </div>
</template>

<script>
    export default {
        props: {
            value: [Object, String, Number],
            label: String,
            noBorder: {},
            options: Array,
            name: String,
        },

        data() {
            return {
                value_: {value: null, label: null},
                options_: [],
                score: 0,
                errors: {},
                isInvalid: false,
                isFocused: false,
                isDropDownOpen: false,
                hasBorder: true,
            }
        },

        mounted() {
            this.select(this.value, false)

            window.addEventListener('click', () => {
                if (this.isDropDownOpen) this.isDropDownOpen = false
            })

            if (this.options)
            {
                this.parseOptions(this.options)
                this.select(this.value, false)
            }

            if (typeof this.noBorder !== 'undefined') this.hasBorder = false

            this.validate()
        },

        watch: {
            value() {
                this.select(this.value, false)
                this.validate()
            },

            options() {
                if (this.options)
                {
                    this.parseOptions(this.options)
                    this.select(this.value, false)
                }
            }
        },

        computed: {
            isFocusedOrFilled() {
                return ((this.value_.label && this.value_.label.length > 0) || this.isFocused)
            },

            displayValue() {
                return 
            }
        },

        methods: {
            parseOptions(options) {
                this.options_ = []

                for (const item of options)
                {
                    if(typeof item !== 'object')
                    {
                        this.options_.push({value: item, label: item})
                    }
                    else if (typeof item === 'object' && !item.hasOwnProperty('value') && !item.hasOwnProperty('label'))
                    {
                        let key = Object.keys(item)[0]
                        this.options_.push({value: key, label: item[key]})
                    }
                    else if (typeof item === 'object' && item.hasOwnProperty('value') && item.hasOwnProperty('label'))
                    {
                        this.options_.push(item)
                    }
                }
            },

            select(value, shouldEmit = true) {

                let newValue = {value: null, label: null}

                for (const option of this.options_)
                {
                    if (option.value === value)
                    {
                        Object.assign(newValue, option)
                        break
                    }
                }

                if (shouldEmit)
                {
                    this.$emit('input', newValue.value)

                    if (this.value_.value != newValue.value)
                    {
                        this.$emit('change', newValue.value)
                    }
                }

                this.value_ = newValue

                this.validate()
                this.isDropDownOpen = false
            },

            validate() {
                this.isInvalid = (Object.keys(this.errors).length > 0)
            },
        },
    }
</script>

<style lang="sass" scoped>
    .ui-container
        --height: 50px

        height: var(--height)
        width: 100%
        background: var(--bg)
        border-radius: 5px
        position: relative
        z-index: 10

        &.focused
            z-index: 11
        
            .border
                border: var(--focused-input-border)

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
            line-height: calc(var(--height) + 3px)
            position: absolute
            top: 0
            left: 0
            padding: 0 30px 0 15px
            white-space: nowrap
            overflow: hidden
            text-overflow: ellipsis
            text-align: left
            pointer-events: none
            transition: all 200ms
            color: var(--text-gray)
            transform-origin: top left

        .input
            height: 100%
            width: 100%
            padding: 0 30px 0 15px
            border: none
            background: none
            border-radius: 3px
            font-family: rubik
            font-size: 16px
            pointer-events: none
            color: var(--heading-gray)

        .dropdown
            position: absolute
            top: calc(var(--height) + 10px)
            left: 0
            width: 100%
            max-height: 400px
            overflow: hidden
            overflow-y: auto
            background: var(--bg)
            border: var(--input-border)
            border-radius: 5px
            padding: 10px 0
            transition: all 100ms
            transform: scaleY(0) translateY(-10px)
            transform-origin: top center
            text-align: left

            &.open
                transform: scaleY(1) translateY(0px)

            .item
                width: 100%
                font-size: 14px
                color: var(--heading-gray)
                padding: 10px 15px 7px
                line-height: 22px
                cursor: pointer
                user-select: none

                &:hover
                    background: var(--primary-shade)
                    color: var(--primary)

                &.selected
                    background: var(--primary)
                    color: white

        .dropdown-toggle
            height: var(--height)
            width: 30px
            line-height: var(--height)
            text-align: center
            font-family: 'Material Icons'
            font-size: 22px
            color: var(--text-gray)
            background: transparent
            border-radius: 5px
            position: absolute
            top: 0
            right: 0
            z-index: 1
            pointer-events: none
            transition: all 100ms
            padding: 0
</style>