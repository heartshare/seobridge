<template>
    <transition name="fly-in" :duration="200">
        <div class="container" v-show="isOpen">
            <div class="background" @click="close(true)"></div>
            <div class="main-container">
                <div class="content-container">
                    <div class="headline" v-if="$slots.heading">
                        <slot name="heading"></slot>
                    </div>

                    <div class="text" v-if="$slots.default">
                        <slot></slot>
                    </div>

                    <form @submit.stop.prevent>
                        <div class="inputs" v-if="$slots.inputs">
                            <slot name="inputs"></slot>
                        </div>
                    </form>
                </div>
                
                <div class="button-container">
                    <span class="left">
                        <slot name="button-1"></slot>
                    </span>
                    <span class="right">
                        <slot name="button-2"></slot>
                    </span>
                </div>
            </div>

            <ui-toast class="overlay-toasts bottom left" ref="overlayToasts"></ui-toast>
        </div>
    </transition>
</template>

<script>
    export default {
        data() {
            return {
                isOpen: false,
            }
        },

        methods: {
            open() {
                this.isOpen = true
            },

            close(shouldEmit = false) {
                this.isOpen = false
                if (shouldEmit) this.$emit('close')
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

        &.fly-in-enter,
        &.fly-in-leave-to
            .main-container
                transform: translate(-50%, -50%) scale(0)
                opacity: 0

            .background
                opacity: 0

        .background
            position: fixed
            top: 0
            left: 0
            width: 100%
            height: 100%
            background: #00000060
            transition: all 200ms

        .main-container
            position: absolute
            top: 50%
            left: 50%
            transform: translate(-50%, -50%)
            max-height: calc(100vh - 60px)
            width: calc(100% - 60px)
            max-width: 500px
            background: var(--bg)
            border-radius: 7px
            text-align: left
            overflow: hidden
            transition: all 200ms
            filter: var(--elevation-4)
            will-change: transform, opacity
            display: flex
            flex-direction: column

            .content-container
                flex: 1
                display: flex
                flex-direction: column
                gap: 10px
                padding: 15px

                .text
                    font-size: var(--text-size)
                    color: var(--text-gray)
                    user-select: none
                    line-height: 150%

                .headline
                    font-size: 20px
                    font-family: var(--heading-font)
                    color: var(--heading-gray)
                    user-select: none
                    line-height: 150%
                    padding-bottom: 4px

                .inputs
                    width: 100%
                    display: flex
                    flex-direction: column
                    gap: 15px

            .button-container
                background: var(--bg-dark)
                padding: 15px
                width: 100%
                text-align: right

                .left
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