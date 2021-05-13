<template>
    <div class="ccc-ui-container">
        <div class="tab-wrapper">
            <div class="tab" v-for="(tab, i) in tabs" :key="i" :ref="i" :class="{'active': i === value_}" @click="select(i)">
                {{tab}}
            </div>
        </div>
        <div class="indicator" ref="indicator"></div>
    </div>
</template>

<script>
    export default {
        props: {
            tabs: Object,
            value: String,
        },

        data() {
            return {
                value_: null
            }
        },

        mounted() {
            this.value_ = this.value

            setTimeout(() => {
                this.setIndicator(this.value_)
            }, 200)
        },

        watch: {
            value() {
                this.value_ = this.value
                this.setIndicator(this.value_)
            },
        },

        methods: {
            select(val){
                this.value_ = val
                this.$emit('input', val)
            },

            setIndicator(val) {
                if (this.$refs.hasOwnProperty(val))
                {
                    let tab = this.$refs[val][0]
                    let indicator = this.$refs.indicator

                    indicator.style.width = tab.offsetWidth - 30 + 'px'
                    indicator.style.left = tab.offsetLeft + 15 + 'px'
                }
            },
        },
    }
</script>

<style lang="sass" scoped>
    .ccc-ui-container
        display: block
        width: 100%
        border-bottom: var(--border)
        position: relative

        .indicator
            position: absolute
            bottom: 0
            left: 0
            width: 0
            height: 3px
            background: var(--primary)
            border-radius: 10px 10px 0 0
            transition: all 200ms cubic-bezier(0.34,-0.01, 0.36, 1)
            pointer-events: none

        .tab-wrapper
            width: 100%
            display: flex
            height: 46px
            gap: 10px

            .tab
                height: 46px
                padding: 0 15px
                display: grid
                letter-spacing: 0.5px
                font-weight: 700
                font-size: 13px
                text-transform: uppercase
                place-content: center
                user-select: none
                cursor: pointer
                border-radius: 4px 4px 0 0
                transition: background 200ms

                &:hover
                    color: var(--heading-gray)

                &.active
                    // background: var(--primary-shade)
                    color: var(--primary)
</style>