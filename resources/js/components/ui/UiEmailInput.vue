<template>
    <div class="container" :class="[{'focused': isFocused}, {'focused-or-filled': isFocusedOrFilled}, {'invalid': isInvalid}, {'has-label': label}]">
        <input class="input" :autocomplete="ac" :name="name" v-model="value_" type="email" @input="emit()" @focus="isFocused = true" @blur="isFocused = false">
        
        <div class="border"></div>

        <div class="label">{{label}}</div>

        <div class="errors" v-show="isInvalid">
            <span v-if="errors.format">Invalid email</span>
            <span v-else-if="errors.taken">Email already taken</span>
        </div>
        
        <ui-spinner class="loader" v-show="isLoading" color="var(--secondary)" size="30" stroke="4"></ui-spinner>
    </div>
</template>

<script>
    export default {
        props: {
            value: String,
            label: String,
            name: {},
            availability: {},
            ac: String,
            url: {
                type: String,
                default: '/auth/email-available',
            },
        },

        data() {
            return {
                value_: '',
                errors: {},
                isLoading: false,
                isInvalid: false,
                isFocused: false,
                checkAvailability: false,
            }
        },

        mounted() {
            this.value_ = (typeof this.value === 'string') ? this.value : ''
            if (typeof this.availability !== 'undefined') this.checkAvailability = true
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
            },
        },

        methods: {
            emit() {
                this.$emit('input', this.value_)
                this.validate()
            },

            validate() {

                // Input is empty
                if (!this.value_.length > 0)
                {
                    delete this.errors.taken
                    delete this.errors.format
                    this.isLoading = false
                    this.isInvalid = (Object.keys(this.errors).length > 0)
                    return
                }

                // Email regex
                if (!/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z0-9]{2,10})$/.test(this.value_)) 
                {
                    this.errors.format = true
                }
                else
                {
                    delete this.errors.format
                }
                
                // Compress errors
                this.isInvalid = (Object.keys(this.errors).length > 0)
                
                // Availability check
                if (!this.errors.format && this.checkAvailability && this.url)
                {
                    this.isLoading = true
                    this.debouncedValidation()
                }
            },

            debouncedValidation: _.debounce(function() {

                if (this.value_.length <= 0)
                {
                    this.isLoading = false
                    return
                }

                axios.post(this.url, { email: this.value_ }).then((response) => {
                    
                    if( !response.data ) 
                    {
                        this.errors.taken = true
                    }
                    else
                    {
                        delete this.errors.taken
                    }
                    
                    this.isLoading = false
                    this.isInvalid = (Object.keys(this.errors).length > 0)
                })
                

            }, 1500),
        },
    }
</script>

<style lang="sass" scoped>
    .container
        --height: 50px

        height: var(--height)
        width: 100%
        background: var(--bg)
        border-radius: 5px
        position: relative

        &.focused
            .border
                border-color: var(--focused-input-border)

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
            color: var(--text-gray)
            transform-origin: top left

        .input
            height: 100%
            width: 100%
            padding: 0 var(--height) 0 15px
            border: none
            background: none
            border-radius: 3px
            font-family: var(--text-font)
            font-size: 16px
            color: var(--heading-gray)

        .loader
            position: absolute
            top: calc((var(--height) - 30px) / 2)
            right: calc((var(--height) - 30px) / 2)
            z-index: 1
            pointer-events: none

        .errors
            font-size: 10px
            max-width: calc(100% - 22px)
            line-height: 16px
            position: absolute
            left: 11px
            bottom: -16px
            text-align: right
            white-space: nowrap
            overflow: hidden
            text-overflow: ellipsis
            pointer-events: none
            color: var(--error)
</style>