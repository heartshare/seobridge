<template>
    <v-popover class="popover-container" placement="bottom-end" trigger="click" offset="0px" popoverClass="popover-menu" @li-clicked="click()">
        <slot name="trigger"></slot>
        <template v-slot:popover>
            <slot></slot>
        </template>
    </v-popover>
</template>

<script>
    export default {
        methods: {
            click() {},
        },
    }
</script>

<style lang="sass">
    .popover-container
        display: contents

        .trigger
            display: contents
            
    .popover-menu
        margin: 0 !important

        .wrapper
            .tooltip-arrow
                display: none

            .tooltip-inner
                background: white
                border-radius: 5px
                filter: var(--elevation-1)
                box-shadow: none
                padding: 10px 0
</style>