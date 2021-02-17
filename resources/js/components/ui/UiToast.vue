<template>
    <div class="ui-container" @click.stop>
        <div class="toast" v-for="(toast, i) in toasts" :key="i" :class="[{'has-icon': toast.hasIcon && toast.type}, toast.type]" @mouseover="toast.mouseOver()" @mouseout="toast.mouseOut()">
            <div class="icon info">&#62204;</div>
            <div class="icon error">&#61785;</div>
            <div class="icon warning">&#61478;</div>
            <div class="icon success">&#62944;</div>

            <div class="title">{{toast.title}}</div>
            <div class="description" v-if="typeof toast.description === 'string'">{{toast.description}}</div>

            <ui-icon-button class="close-button" :class="{'display': ['both', 'manual'].includes(toast.options.mode)}" @click="toast.remove()">&#61782;</ui-icon-button>

            <ui-progress-ring class="progress" :class="{'display': ['both', 'timed'].includes(toast.options.mode)}" color="var(--secondary)" stroke="2.5" :size="toast.options.mode === 'timed' ? 20 : 40" :progress="toast.progress"></ui-progress-ring>
        </div>
    </div>
</template>
<script>
    class Toast
    {
        constructor(vm, type, title, description = null, options = {})
        {
            this.vm = vm
            this.hover = false
            this.timeout = {
                remaining: 0,
                duration: 0,
                running: false,
            }
            this.lastTick = Date.now()
            this.updateId = null,
            this.progress = 100,

            this.default = {
                duration: 2000,
            }

            this.type = type
            this.title = title
            this.options = {
                mode: 'both',
                hover: null,
                hasIcon: true,
                duration: this.default.duration,
            }
            this.description = description && description.data && description.data.message ? description.data.message : description

            this.options.mode =     options.hasOwnProperty('mode')      && ['both', 'manual', 'timed'].includes(options.mode) ? options.mode : 'both'
            this.options.hover =    options.hasOwnProperty('hover')     && ['stop', null].includes(options.hover) ? options.hover : null
            this.options.hasIcon =  options.hasOwnProperty('hasIcon')   && typeof options.hasIcon === 'boolean' ? options.hasIcon : true
            this.options.duration = options.hasOwnProperty('duration')  && typeof options.duration === 'number' ? options.duration : this.default.duration

            if (this.hasTimeout())
            {
                this.timeout.duration = this.options.duration
                this.timeout.remaining = this.options.duration
                this.timeout.running = true

                this.updateId = setInterval(() => { this.update() }, 1)
            }
        }

        hasTimeout()
        {
            return ['both', 'timed'].includes(this.options.mode)
        }



        update()
        {
            let tick = Date.now()

            if (this.timeout.running) this.timeout.remaining -= (tick - this.lastTick)

            this.lastTick = tick
            this.progress = 100 - this.timeout.remaining / this.timeout.duration * 100

            if (this.timeout.remaining < 0) this.remove()
        }



        mouseOver()
        {
            this.hover = true
            if (this.hasTimeout() && this.options.hover === 'stop') this.timeout.running = false
        }

        mouseOut()
        {
            this.hover = false
            if (this.hasTimeout()) this.timeout.running = true
        }



        remove()
        {
            clearInterval(this.updateId)
            this.vm.toasts.splice(this.vm.toasts.indexOf(this), 1)
            this.vm.$forceUpdate()
        }
    }





    export default {
        props: {
            stack: String,
            outAnimation: String,
        },

        data() {
            return {
                stackFrom: 'bottom',
                toasts: [],
            }
        },

        mounted() {
            this.stackFrom = (typeof this.stack === 'undefined') ? null : this.stack
        },

        methods: {
            addToast(type, title, description = null, options = {}) {

                let toast = new Toast(this, type, title, description, options)
                
                if (this.stack === 'top')
                {
                    this.toasts.unshift(toast)
                }
                else if (this.stack === 'bottom')
                {
                    this.toasts.push(toast)
                }
                else
                {
                    this.clear()
                    this.toasts = [toast]
                }
            },

            clear() {
                for (const toastClass of this.toasts)
                {
                    toastClass.remove()
                }
            },
        },
    }
</script>
<style lang="sass" scoped>
    .ui-container
        position: absolute
        width: 100%
        
        &.bottom
            bottom: 0
        &.top
            top: 0
        &.left
            left: 0
        &.right
            right: 0

        .toast
            width: 100%
            border-radius: 7px
            background: white
            box-shadow: 0 3px 6px #00000030
            position: relative
            padding: 20px 50px 20px 20px
            color: var(--secondary)
            text-align: left
            margin-top: 20px

            &:first-of-type
                margin-top: 0

            &.has-icon
                padding-left: 50px

            &.warning
                color: var(--warning)
                &.has-icon .icon.warning
                    display: grid

            &.error
                color: var(--error)
                &.has-icon .icon.error
                    display: grid

            &.info
                color: var(--primary)
                &.has-icon .icon.info
                    display: grid

            &.success
                color: var(--success)
                &.has-icon .icon.success
                    display: grid

            &::before
                content: ''
                position: absolute
                top: 10px
                left: 0
                background: currentcolor
                height: calc(100% - 20px)
                width: 5px
                border-radius: 0 10px 10px 0

            .icon
                height: 100%
                width: 50px
                position: absolute
                top: 0
                left: 0
                display: none
                align-content: center
                justify-content: center
                font-family: 'Material Icons'
                font-size: 26px
                color: inherit
                user-select: none
                pointer-events: none

            .title
                font-size: 16px
                font-weight: 600
                line-height: 150%
                padding-top: 3px
                display: block
                letter-spacing: 1px

            .description
                font-size: 14px
                line-height: 150%
                display: block
                color: var(--secondary)

            .close-button
                position: absolute
                top: 5px
                right: 5px
                color: var(--secondary)
                display: none

                &:hover
                    background: var(--secondary-shade)

                &.display
                    display: block

            .progress
                position: absolute
                top: 5px
                right: 5px
                pointer-events: none
                opacity: 0.5
                display: none

                &.display
                    display: block
</style>