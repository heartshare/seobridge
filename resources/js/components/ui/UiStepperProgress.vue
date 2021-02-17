<template>
    <div class="stepper-progress-container">
        <div class="progress" v-for="(step, i) in steps" :key="i" :style="'flex:'+step" :class="[{'full': i < doneSteps}, {'empty': i > doneSteps}]">
            <div class="progress-bar" :style="'width:' + stepProgress + '%'"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            steps: {
                type: Array
            },
            progress: {
                type: Number,
                default: 0
            }
        },

        computed: {
            doneSteps() {
                return (this.progress - this.progress % 100) / 100
            },
            stepProgress() {
                return this.progress % 100
            }
        }
    }
</script>

<style lang="sass" scoped>
    .stepper-progress-container
        width: 100%
        height: 100%
        display: flex
        column-gap: 4px

        .progress
            background: #ddd
            position: relative
            overflow: hidden
            border-radius: 10px

            .progress-bar
                position: absolute
                top: 0
                left: 0
                height: 100%
                width: 0%
                border-radius: 10px
                background: var(--primary)
                transition: width 200ms

            &.full .progress-bar
                width: 100% !important
            &.empty .progress-bar
                width: 0% !important
</style>