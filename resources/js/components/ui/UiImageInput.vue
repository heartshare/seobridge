<template>
    <label class="ccc-ui-container" :class="{'loading': loading}">
        <div class="img" :style="'background-image: url(' + image + '); padding-top:' + aspect_"></div>
        <div class="icon">&#61696;</div>
        <ui-spinner class="spinner" size="30" color="white" v-show="loading"></ui-spinner>
        <input type="file" accept="image/*" ref="input" v-on:input="input()">
    </label>
</template>

<script>
    export default {
        props: {
            aspect: {
                type: Array
            },

            value: {},

            image: {
                type: String
            },

            loading: {
                type: Boolean
            },
        },

        data() {
            return {
                aspect_: 1
            }
        },

        mounted() {
            this.aspect_ = (typeof this.aspect === 'object' && this.aspect.length === 2) ? this.aspect[1] / this.aspect[0] * 100 + '%' : '100%'
        },

        methods: {
            input() {
                if (this.$refs.input.files[0])
                {
                    let file = this.$refs.input.files[0]
                    
                    this.$emit('select', file)
    
                    this.$refs.input.value = ''
                }
            }
        },
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        width: 100%
        overflow: hidden
        position: relative
        cursor: pointer

        &:hover
            .img
                filter: brightness(0.6)
                transform: scale(1.07)
            .icon
                opacity: 1

        &.loading
            .img
                filter: brightness(0.6)
                transform: scale(1.07)
            .icon
                opacity: 0 !important

        .img
            width: 100%
            border: none
            pointer-events: none
            user-select: none
            transition: transform 160ms, filter 160ms
            background-size: cover
            background-repeat: no-repeat
            background-position: center

        .spinner
            position: absolute
            top: calc(50% - 15px)
            left: calc(50% - 15px)
            pointer-events: none

        .icon
            height: 50px
            line-height: 50px
            width: 50px
            text-align: center
            color: #ffffffdd
            font-family: 'Material Icons'
            font-size: 36px
            position: absolute
            top: calc(50% - 25px)
            left: calc(50% - 25px)
            opacity: 0
            transition: opacity 160ms
            pointer-events: none

        input
            display: none
</style>