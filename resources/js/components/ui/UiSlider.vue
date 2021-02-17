<template>
    <div class="ui-container" @mousedown="down($event)" ref="input">
        <div class="bar" ref="bar">
            <div class="progress" :style="'width: '+percentage+'%;'"></div>
            <div class="handle" :style="'left: '+percentage+'%;'"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            min: {
                type: Number,
                default: 0,
            },
            max: {
                type: Number,
                default: 100,
            },
            step: {
                type: Number,
                default: 1,
            },
            value: {}
        },

        data() {
            return {
                value_: 0,
                percentage: 0,
                isGrabbing: false,
            }
        },

        mounted() {
            this.calculate(this.value && !isNaN(this.value) ? parseFloat(this.value) : this.value)

            window.addEventListener('mouseup', () => {
                this.up()
            })

            window.addEventListener('mousemove', (event) => {
                this.move(event)
            })
        },

        watch: {
            value() {
                if (!this.isGrabbing) this.calculate(this.value && !isNaN(this.value) ? parseFloat(this.value) : this.value_)
            }
        },

        methods: {
            calculate(value) {
                this.value_ = this.clamp(value, this.min, this.max)
                this.value_ = this.value_ - this.value_ % this.step
                this.percentage = this.clamp((this.value_ - this.min) / (this.max - this.min) * 100, 0, 100)

            },

            clamp(value, min, max) {
                return value <= min ? min : value >= max ? max : value;
            },



            down(event) {
                this.isGrabbing = true

                let max = this.$refs.bar.clientWidth
                let pos = event.clientX - this.$refs.bar.getBoundingClientRect().left
                let percentage = this.clamp(pos / max, 0, 1)
                let value = (this.max - this.min) * percentage + this.min

                this.calculate(value)
                this.emit()
            },

            move(event) {
                if (!this.isGrabbing)
                {
                    return
                }

                let max = this.$refs.bar.clientWidth
                let pos = event.clientX - this.$refs.bar.getBoundingClientRect().left
                let percentage = this.clamp(pos / max, 0, 1)
                let value = (this.max - this.min) * percentage + this.min

                this.calculate(value)
                this.emit()
            },

            up() {
                this.isGrabbing = false
            },

            emit(value = this.value_) {
                this.$emit('input', parseFloat(value))
            },
        },
    }
</script>

<style lang="sass" scoped>
    .ui-container
        --height: 30px
        height: var(--height)
        width: 100%
        position: relative

        .bar
            width: calc(100% - var(--height))
            height: 2px
            position: absolute
            top: calc(50% - 1px)
            left: calc(var(--height) / 2)
            cursor: pointer
            background: #ccc
            border-radius: var(--height)
            pointer-events: none

            .progress
                position: absolute
                top: 0
                left: 0
                height: 100%
                max-width: 100%
                background: var(--primary)
                border-radius: var(--height)
                pointer-events: none

            .handle
                position: absolute
                top: 0
                height: 20px
                width: 20px
                border-radius: 100%
                background: white
                box-shadow: 0 3px 5px #00000040
                transform: translate(-50%, -50%)
                pointer-events: none
</style>