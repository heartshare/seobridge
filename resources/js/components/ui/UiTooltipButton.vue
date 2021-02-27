<template>
    <v-popover placement="bottom-end" trigger="hover" popoverClass="text-tooltip">
        <div class="ccc-ui-container" :class="classes">
            &#983805;
        </div>
        <template v-slot:popover>
            <slot></slot>
        </template>
    </v-popover>
</template>

<script>
    export default {
        props: ['solid', 'error', 'success', 'warning', 'type', 'content'],

        data() {
            return {
                classes: [],
            }
        },

        mounted() {
            if (typeof this.solid !== 'undefined') this.classes.push('solid')
            if (typeof this.error !== 'undefined' || this.type === 'error') this.classes.push('error')
            else if (typeof this.success !== 'undefined' || this.type === 'success') this.classes.push('success')
            else if (typeof this.warning !== 'undefined' || this.type === 'warning') this.classes.push('warning')
        },
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        height: 30px
        line-height: 30px
        width: 30px
        padding: 0
        font-size: 20px !important
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

        color: var(--text-gray)
        background: transparent

        &:focus
            &::after
                content: ''
                top: -4px
                left: -4px
                border: 2px solid var(--primary)
                width: calc(100% + 4px)
                height: calc(100% + 4px)
                position: absolute
                border-radius: 100%

        &:hover
            background: var(--primary-shade)

        &.error
            color: var(--error)

            &::after
                border-color: var(--error)

            &:hover
                background: var(--error-shade)

        &.success
            color: var(--success)

            &::after
                border-color: var(--success)

            &:hover
                background: var(--success-shade)

        &.warning
            color: var(--warning)

            &::after
                border-color: var(--warning)

            &:hover
                background: var(--warning-shade)

        &.solid
            background: var(--primary-shade)
            color: var(--primary)

            &:hover
                background: var(--primary)
                color: white
                box-shadow: 0 5px 7px #00000020

            &.error
                background: var(--error-shade)
                color: var(--error)

                &:hover
                    background: var(--error)

            &.success
                background: var(--success-shade)
                color: var(--success)

                &:hover
                    background: var(--success)

            &.warning
                background: var(--warning-shade)
                color: var(--warning)

                &:hover
                    background: var(--warning)
</style>