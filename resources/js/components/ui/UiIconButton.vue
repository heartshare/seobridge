<template>
    <button class="ccc-ui-container" :class="[...classes, {'disabled': disabled_}, {'loading': loading_}]" @click="click()">
        <span class="content">
            <slot></slot>
        </span>
        <ui-spinner class="spinner" v-show="loading_" color="var(--disabled-color)"></ui-spinner>
        <div class="border" v-if="hasBorder"></div>
        <div class="bg-overlay"></div>
    </button>
</template>

<script>
    export default {
        props: ['solid', 'info', 'error', 'border', 'loading', 'disabled'],

        data() {
            return {
                classes: [],
                hasBorder: false,
                disabled_: false,
                loading_: false,
            }
        },

        mounted() {
            this.init()
        },

        watch: {
            border()    {this.init()},
            solid()     {this.init()},
            error()     {this.init()},
            info()      {this.init()},
            loading()   {this.init()},
            disabled()  {this.init()},
        },

        methods: {
            init()
            {
                this.classes = []
                
                if      (this.solid == true || (typeof this.solid !== 'undefined'    && this.solid === ''))    this.classes.push('solid')
                if      (this.info == true  || (typeof this.info !== 'undefined'     && this.info === ''))     this.classes.push('info')
                else if (this.error == true || (typeof this.error !== 'undefined'    && this.error === ''))    this.classes.push('error')

                this.hasBorder = (this.border == true   || (typeof this.border   !== 'undefined' && this.border === ''))
                this.disabled_ = (this.disabled == true || (typeof this.disabled !== 'undefined' && this.disabled === ''))
                this.loading_  = (this.loading == true  || (typeof this.loading  !== 'undefined' && this.loading === ''))
            },

            click() {
                if (this.disabled_ || this.loading_)
                {
                    return
                }

                this.$emit('click')
            },
        },
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        height: 40px
        line-height: 40px
        width: 40px
        padding: 0
        font-size: 20px
        text-align: center
        font-family: 'Material Icons'
        text-transform: uppercase
        border-radius: 100%
        border: none
        cursor: pointer
        user-select: none
        transition: all 100ms
        vertical-align: top
        position: relative
        color: var(--heading-gray)
        background: transparent

        &:hover
            .bg-overlay
                opacity: 0.1

        &:focus
            .bg-overlay
                opacity: 0.17

        &.info
            color: var(--primary)

            .border
                border-color: var(--primary)

        &.error
            color: var(--error)

            .border
                border-color: var(--error)

        &.solid
            background: var(--heading-gray)
            color: var(--bg)

            &.info
                background: var(--primary)
                color: white

            &.error
                background: var(--error)
                color: white

        &.disabled
            color: var(--disabled-color) !important
            background: transparent !important
            pointer-events: none

            &.solid
                background: var(--disabled-bg) !important

            .border
                border-color: var(--disabled-border-color) !important

        &.loading
            color: var(--disabled-color) !important
            background: transparent !important
            pointer-events: none

            &.solid
                background: var(--disabled-bg) !important

            .border
                border-color: var(--disabled-border-color) !important

            .content
                opacity: 0

        .border
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 500px
            border: 1px solid var(--heading-gray)
            pointer-events: none

        .bg-overlay
            height: 100%
            width: 100%
            position: absolute
            top: 0
            left: 0
            border-radius: 500px
            background: currentcolor
            opacity: 0
            transition: opacity 100ms

        .spinner
            position: absolute
            top: calc(50% - 10px)
            left: calc(50% - 10px)
</style>