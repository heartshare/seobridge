<template>
    <div class="ui-container">
        <input type="tel" class="input" :value="amount" @input="input($event)">
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Number
            },
        },

        data() {
            return {
                amount: '0',
                max: 10000,
                num: 0,
            }
        },

        mounted() {
            this.sanitizeAndSetValue(this.value ? this.value : this.amount)
        },

        watch: {
            value() {
                this.sanitizeAndSetValue(this.value)
            },
        },

        methods: {
            input(event) {

                this.amount += event.data ? event.data : ''
                this.amount = this.amount.replace(/[^0-9]/g, '')

                if( event.inputType === 'deleteContentBackward' )
                {
                    this.amount = this.amount.substring(0, this.amount.length-1)
                }

                this.amount = parseFloat(this.amount) / 100


                if( this.amount > this.max ) this.amount = this.max
                if( this.amount < 0 ) this.amount = 0

                let emittedValue = Math.round(this.amount * 100)
                this.num = emittedValue
                
                this.amount = new Intl.NumberFormat('en-US', { style: 'decimal', currency: 'USD', minimumFractionDigits: 2, maximumFractionDigits: 2}).format(this.amount)

                this.$emit('input', emittedValue)
            },

            sanitizeAndSetValue(value = 0, shouldEmit = false) {
                value += ''
                value = value.replace(/[^0-9]/g, '')

                value = parseFloat(value) / 100

                if(value > this.max) value = this.max
                if(value < 0) value = 0
                
                this.amount = new Intl.NumberFormat('en-US', { style: 'decimal', currency: 'USD', minimumFractionDigits: 2, maximumFractionDigits: 2}).format(value)

                this.num = Math.round(value * 100)

                if(shouldEmit)
                {
                    this.$emit('input', Math.floor(value * 100))
                }
            }
        },
    }
</script>

<style lang="sass" scoped>
    .ui-container
        width: 100%
        background: white
        z-index: 1
        font-family: rubik
        border-radius: 5px
        user-select: none
        position: relative
        vertical-align: top

        .input
            width: 100%
            height: 50px
            background: none
            text-align: center
            border: var(--input-border)
            font-family: rubik
            font-size: 18px
            padding-top: 5px
            user-select: none
            border-radius: 5px

            &:focus
                border-color: #666
</style>