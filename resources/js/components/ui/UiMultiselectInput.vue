<template>
    <div class="ui-container" tabindex="0" @click.stop="isDropDownOpen = !isDropDownOpen" @focus="isFocused = true" @blur="isFocused = false; isDropDownOpen = false" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}]">        
        <input class="input" disabled :name="name" :value="displayValue" type="text" spellcheck="false">
        <div class="border"></div>

        <div class="label">{{label}}</div>

        <div class="dropdown" :class="{'open': isDropDownOpen}" @click.stop>
            <div class="item" v-for="(item, i) in options_" :key="i" @click="toggleSelect([item.value])" :class="{'selected': value_.includes(item.value)}">
                {{item.label}}
            </div>
        </div>
        
        <div class="dropdown-toggle">&#61760;</div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Array,
                default: () => { return [] },
            },
            label: String,
            options: Array,
            name: String,
        },

        data() {
            return {
                value_: [],
                options_: [],
                score: 0,
                errors: {},
                isInvalid: false,
                isFocused: false,
                isDropDownOpen: false,
            }
        },

        mounted() {
            this.syncSelect(this.value, false)

            window.addEventListener('click', () => {
                if (this.isDropDownOpen) this.isDropDownOpen = false
            })

            if (this.options)
            {
                this.parseOptions(this.options)
            }

            this.validate()
        },

        watch: {
            value() {
                this.syncSelect(this.value, false)
                this.validate()
            },
            options() {
                if (this.options)
                {
                    this.parseOptions(this.options)
                }
            }
        },

        computed: {
            isFocusedOrFilled() {
                return ((this.value_.length > 0) || this.isDropDownOpen)
            },

            displayValue() {
                let out = []

                for (const item of this.value_)
                {
                    let label = this.options_.find(e => e.value === item)
                    out.push(label ? label.label : item)
                }

                return out.join(' • ')
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

            toggleSelect(items, shouldEmit = true) {
                for (const item of items)
                {
                    let i = this.value_.findIndex(e => e === item)

                    if (i >= 0)
                    {
                        this.value_.splice(i, 1)
                    }
                    else
                    {
                        this.value_.push(item)
                    }
                }

                if (shouldEmit)
                {
                    this.$emit('input', this.value_)
                    // this.$emit('change', this.value_)
                }

                this.validate()
            },

            syncSelect(items, shouldEmit = true) {
                this.value_ = [...items]

                if (shouldEmit)
                {
                    this.$emit('input', this.value_)
                }

                this.validate()
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
        background: white
        border-radius: 5px
        position: relative
        z-index: 10

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
            line-height: calc(var(--height) + 3px)
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

        .input
            height: 100%
            width: 100%
            padding: 0 var(--height) 0 15px
            border: none
            background: none
            border-radius: 3px
            font-family: rubik
            font-size: 13px
            pointer-events: none
            user-select: none
            color: black

        .dropdown
            position: absolute
            top: calc(var(--height) + 10px)
            left: 0
            width: 100%
            max-height: 400px
            overflow: hidden
            overflow-y: auto
            background: white
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
                color: black
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
            color: var(--secondary)
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