<template>
    <button class="ccc-ui-container" :class="[...classes, {'disabled': disabled_}, {'loading': loading_}]" @click="click()">
        <span class="content">
            <slot></slot>
        </span>
        <ui-spinner class="spinner" v-show="loading_" color="var(--disabled-color)"></ui-spinner>
        <div class="icon" v-if="icon_" v-html="icon_"></div>
        <div class="border" v-if="hasBorder"></div>
        <div class="bg-overlay"></div>
    </button>
</template>

<script>
    export default {
        props: ['text', 'error', 'icon', 'icon-left', 'small', 'href', 'border', 'loading', 'disabled'],

        data() {
            return {
                classes: [],
                hasBorder: false,
                icon_: null,
                disabled_: false,
                loading_: false,
            }
        },

        created() {
            this.init()
        },

        watch: {
            iconLeft()  {this.init()},
            text()      {this.init()},
            error()     {this.init()},
            icon()      {this.init()},
            loading()   {this.init()},
            disabled()  {this.init()},
        },

        methods: {
            init()
            {
                this.classes = []
                
                if (this.iconLeft == true || (typeof this.iconLeft !== 'undefined' && this.iconLeft === '')) this.classes.push('icon-left')
                if (this.small == true    || (typeof this.small !== 'undefined'    && this.small === ''))    this.classes.push('small')
                if (this.text == true     || (typeof this.text !== 'undefined'     && this.text === ''))     this.classes.push('text')
                if (this.error == true    || (typeof this.error !== 'undefined'    && this.error === ''))    this.classes.push('error')

                this.hasBorder = (this.border == true   || (typeof this.border   !== 'undefined' && this.border === ''))
                this.disabled_ = (this.disabled == true || (typeof this.disabled !== 'undefined' && this.disabled === ''))
                this.loading_  = (this.loading == true  || (typeof this.loading  !== 'undefined' && this.loading === ''))


                if (this.icon)
                {
                    this.icon_ = this.icon
                }
                else
                {
                    this.classes.push('no-icon')
                }
            },

            click() {
                if (this.disabled_ || this.loading_)
                {
                    return
                }

                if (this.href)
                {
                    window.location = this.href
                }
                else
                {
                    this.$emit('click')
                }
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
        transition: background 100ms, color 100ms
        vertical-align: top
        position: relative

        background: var(--primary)
        color: white

        &:hover
            .bg-overlay
                opacity: 0.1

        &:focus
            .bg-overlay
                opacity: 0.17
            
        &:hover:not(.text)
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

            .border
                border-color: var(--error)

        &.text
            background: transparent
            color: var(--primary)

            &:hover
                // background: var(--primary)
                // color: white

            &.error
                color: var(--error)

                &:hover
                    // background: var(--error)
                    // color: white

        &.disabled
            color: var(--disabled-color) !important
            background: var(--disabled-bg) !important
            pointer-events: none

            &.text
                background: transparent !important

            .border
                border-color: var(--disabled-border-color) !important

        &.loading
            color: var(--disabled-color) !important
            background: var(--disabled-bg) !important
            pointer-events: none

            &.text
                background: transparent !important

            .border
                border-color: var(--disabled-border-color) !important

            .content
                opacity: 0

            .icon
                opacity: 0

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

        .border
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 5px
            border: 1px solid var(--primary)
            pointer-events: none

        .bg-overlay
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 5px
            background: currentcolor
            opacity: 0
            transition: opacity 100ms

        .spinner
            position: absolute
            top: calc(50% - 10px)
            left: calc(50% - 10px)
</style>