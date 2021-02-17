<template>
    <div class="container" v-show="isOpen">
        <div class="background" @click="close()"></div>
        <div class="feedback-container">
            <div class="headline" v-if="headline">{{headline}}</div>
            <div class="text" v-if="desc">{{desc}}</div>

            <div class="inputs" :class="{'first': i === 0}" v-for="(input, i) in inputs_" :key="i">
                <ui-password-input v-if="input.type === 'password' && input.rating" rating :label="input.label" v-model="input.value"></ui-password-input>
                <ui-password-input v-if="input.type === 'password' && !input.rating" :label="input.label" v-model="input.value"></ui-password-input>
                <ui-text-input v-if="input.type === 'text'" :label="input.label" v-model="input.value"></ui-text-input>
            </div>
            
            <div class="button-container">
                <ui-button class="right-button" v-if="option1 && type === 'info' " :icon="option1.icon" @click="triggerOption1()">{{option1.label}}</ui-button>
                <ui-button class="right-button" v-if="option1 && type === 'delete' " error :icon="option1.icon" @click="triggerOption1()">{{option1.label}}</ui-button>
                <ui-button class="left-button" light :icon="option2.icon" icon-left @click="triggerOption2()">{{option2.label}}</ui-button>
            </div>
        </div>

        <ui-toast class="overlay-toasts bottom left" ref="overlayToasts"></ui-toast>
    </div>
</template>

<script>
    export default {
        props: {
            headline: String,
            desc: String,
            type: {
                type: String,
                default: 'info',
            },
            inputs: Array,
            option1: Object,
            option2: Object,
        },

        data() {
            return {
                isOpen: false,
                inputs_: [],
            }
        },

        methods: {
            open() {
                this.isOpen = true

                if (this.inputs && this.inputs.length)
                {
                    this.inputs_ = []

                    for (const input of this.inputs)
                    {
                        if (!input.hasOwnProperty('key'))
                        {
                            continue
                        }

                        if (!input.hasOwnProperty('value'))
                        {
                            input.value = null
                        }
                        
                        this.inputs_.push(JSON.parse(JSON.stringify(input)))
                    }
                }
            },

            close() {
                this.reset()
                this.isOpen = false
                this.$emit('close')
            },

            reset() {
                this.inputs_ = []
            },

            getValues() {
                let ret = {}

                for (const input of this.inputs_)
                {
                    ret[input.key] = input.value
                }

                return JSON.parse(JSON.stringify(ret))
            },

            triggerOption1() {
                this.$emit('option1', this.getValues())
                this.close()
            },

            triggerOption2() {
                this.$emit('option2', this.getValues())
                this.close()
            },
        }
    }
</script>

<style lang="sass" scoped>
    .container
        position: fixed
        top: 0
        left: 0
        width: 100%
        height: 100%
        z-index: 300

        .background
            position: fixed
            top: 0
            left: 0
            width: 100%
            height: 100%
            background: #00000040
            backdrop-filter: blur(20px)

        .feedback-container
            position: absolute
            top: 50%
            left: 50%
            transform: translate(-50%, -50%)
            max-height: calc(100vh - 60px)
            width: calc(100% - 60px)
            max-width: 500px
            background: white
            border-radius: 7px
            padding: 20px 20px 90px
            text-align: left
            overflow: hidden

            .text
                font-size: 16px
                color: var(--secondary)
                user-select: none
                line-height: 150%
                margin-bottom: 10px

            .headline
                font-size: 20px
                font-family: sofia-pro
                color: black
                user-select: none
                line-height: 150%
                margin-bottom: 10px

            .inputs
                width: 100%
                margin-bottom: 10px

                &.first
                    margin-top: 10px

            .button-container
                background: #f4f4f4
                padding: 20px
                width: 100%
                height: 80px
                position: absolute
                bottom: 0
                left: 0
                text-align: right

                .left-button
                    float: left

        .overlay-toasts
            bottom: 20px !important
            z-index: 1000
            max-width: 500px
            left: 50% !important
            transform: translateX(-50%)

    @media screen and ( max-width: 500px )
        .container
            .feedback-container
                width: 100%
                border-radius: 0

            .overlay-toasts
                bottom: 10px !important
                max-width: calc(100% - 20px)
                left: 10px !important
                transform: none
</style>