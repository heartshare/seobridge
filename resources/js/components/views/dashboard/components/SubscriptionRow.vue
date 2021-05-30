<template>
    <div class="row-wrapper">
        <div class="row-header">
            <ui-icon-button v-tooltip="'Make this your standard billing method'" class="active-button" :disabled="isDefault" :class="{'active': isDefault}" @click="$emit('setDefault', method)">&#984270;</ui-icon-button>

            <div class="name">
                <b>{{method.billing_details.name}}</b>
            </div>

            <div class="description">
                <span style="text-transform: uppercase">{{method.card.brand}}</span>&nbsp;&nbsp;&nbsp;**** **** **** {{method.card.last4}}
            </div>

            <div class="tag-wrapper">
                <div class="tag">{{method.type}}</div>
            </div>

            <ui-popover-menu>
                <template v-slot:trigger>
                    <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                </template>

                <ui-menu-item icon="&#984043;" @click="$emit('edit', method)">Edit billing method</ui-menu-item>
                <ui-menu-divider v-if="!isDefault"></ui-menu-divider>
                <ui-menu-item v-if="!isDefault" icon="&#985721;" @click="$emit('delete', method)">Delete billing method</ui-menu-item>
            </ui-popover-menu>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            method: Object,
            isDefault: Boolean,
        },

        data() {
            return {}
        },

        methods: {

        },
    }
</script>

<style lang="sass" scoped>
    .row-wrapper
        width: 100%
        display: flex
        flex-direction: column
        transition: all 300ms
        border-bottom: var(--border)

        &:last-of-type
            border: none

        &.slide-enter
            transform: translateY(-100px)
            opacity: 0

        &.slide-leave-to
            transform: scale(0)
            opacity: 0

        &.slide-leave-active
            position: absolute

        .row-header
            width: 100%
            display: flex
            align-items: center
            padding: 10px 15px
            gap: 10px

            .active-button
                color: var(--text-gray)
                background: none

                &:hover
                    color: var(--heading-gray)
                    background: none

                &.active
                    color: var(--warning) !important
                    background: none

            .tag-wrapper
                flex: 1
                display: grid
                place-content: center right

                .tag
                    font-size: 10px
                    color: var(--primary)
                    background: var(--primary-shade)
                    padding: 1px 8px
                    line-height: 18px
                    border-radius: 30px
                    letter-spacing: 1px
                    font-weight: 600
                    text-transform: uppercase
                    user-select: none
                    vertical-align: top

            .name
                flex: 2
                text-transform: uppercase
                font-weight: 600
                color: var(--heading-gray)

            .description
                flex: 5
                line-height: 20px

            .more-button
                margin: 0
            
            .expand-button
                transition: transform 200ms

                &.expanded
                    transform: rotate(180deg)
</style>