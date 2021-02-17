<template>
    <svg class="ccc-progress-ring-container" :width="size" :height="size">
        <circle class="progress-ring-circle" ref="circle" :stroke="backColor" :stroke-width="stroke || 3" fill="transparent" :r="radius" :cx="size / 2" :cy="size / 2"/>
        <circle class="progress-ring-circle" :class="{'transition': hasTransition}" ref="circle" :stroke="color" :stroke-dashoffset="offset" :stroke-dasharray="circumference+' '+circumference" :stroke-width="stroke || 3" fill="transparent" :r="radius" :cx="size / 2" :cy="size / 2"/>
    </svg>
</template>
<script>
    export default {
        props: {
            color: String,
            backColor: {
                type: String,
                default: 'transparent',
            },
            size: [String, Number],
            stroke: String,
            transition: String,
            progress: Number,
        },

        data() {
            return {
                hasTransition: false
            }
        },

        mounted() {
            if (typeof this.transition !== 'undefined') this.hasTransition = true
        },

        computed: {
            radius() {
                return this.size / 2 - (this.stroke ? this.stroke / 2 : 1.5)
            },

            circumference () {
                return this.radius * 2 * Math.PI
            },

            offset() {
                let progress = this.progress ? this.progress : 0
                return this.circumference - progress / 100 * this.circumference
            }
        }
    }
</script>
<style lang="sass" scoped>
    .ccc-progress-ring-container

        .progress-ring-circle
            transform: rotate(-90deg)
            transform-origin: 50% 50%

            &.transition
                transition: all 300ms cubic-bezier(0.22, 0.61, 0.36, 1)
</style>