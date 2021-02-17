<template>
    <button tabindex="0" class="ccc-ui-container" :class="classes" @click="href ? goto(href) : $emit('click')">
        <slot></slot>
        <div class="icon" v-html="icon"></div>
    </button>
</template>

<script>
    export default {
        props: ['light', 'error', 'success', 'warning', 'icon', 'icon-left', 'small', 'href'],

        data() {
            return {
                classes: [],
                icon_: '&#983124;',
            }
        },

        mounted() {
            this.init()
        },

        watch: {
            iconLeft()  {this.init()},
            light()     {this.init()},
            error()     {this.init()},
            success()   {this.init()},
            waring()    {this.init()},
            icon()      {this.init()},
        },

        methods: {
            init()
            {
                this.classes = []
                if (typeof this.iconLeft !== 'undefined') this.classes.push('icon-left')
                if (typeof this.small !== 'undefined') this.classes.push('small')

                if (typeof this.light !== 'undefined') this.classes.push('light')
                if (typeof this.error !== 'undefined') this.classes.push('error')
                else if (typeof this.warning !== 'undefined') this.classes.push('warning')
                else if (typeof this.success !== 'undefined') this.classes.push('success')

                if (this.icon == 'none')
                {
                    this.classes.push('no-icon')
                }
            },

            goto(href) {
                window.location = href
            },
        }
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        height: 40px
        line-height: 30px
        font-size: 14px
        padding: 5px 15px
        padding-right: 6px
        text-align: center
        letter-spacing: 1.5px
        font-family: var(--text-font)
        font-weight: 500
        text-transform: uppercase
        border-radius: 5px
        border: none
        cursor: pointer
        user-select: none
        transition: all 100ms
        vertical-align: top
        position: relative

        background: var(--primary)
        color: white

        &:focus
            &::after
                content: ''
                top: -4px
                left: -4px
                border: 2px solid var(--primary)
                width: calc(100% + 4px)
                height: calc(100% + 4px)
                position: absolute
                border-radius: 8px

        &:hover
            box-shadow: 0 5px 7px #00000020

        &.no-icon
            padding: 5px 15px

            .icon
                display: none !important

        &.icon-left
            padding: 5px 15px
            padding-left: 6px

            &::first-letter
                margin-left: 5px

            .icon
                float: left

        &.small
            height: 30px
            padding-top: 0
            padding-bottom: 0

        &.error
            background: var(--error)
            color: white

        &.success
            background: var(--success)
            color: white

        &.warning
            background: var(--warning)
            color: white

        &.light
            background: var(--primary-shade)
            color: var(--primary)

            &:hover
                background: var(--primary)
                color: white

            &.error
                background: var(--error-shade)
                color: var(--error)

                &:hover
                    background: var(--error)
                    color: white

            &.success
                background: var(--success-shade)
                color: var(--success)

                &:hover
                    background: var(--success)
                    color: white

            &.warning
                background: var(--warning-shade)
                color: var(--warning)

                &:hover
                    background: var(--warning)
                    color: white


        .icon
            height: 30px
            width: 30px
            line-height: 30px
            text-align: center
            color: inherit
            font-size: 18px
            letter-spacing: 0
            font-weight: normal
            pointer-events: none
            vertical-align: top
            font-family: 'Material Icons'
</style>