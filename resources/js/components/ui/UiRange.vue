<template>
    <div class="ui-container" ref="input">
        <div class="bar" ref="bar">
            <div class="progress" :style="'left: '+percentage[0]+'%; width: '+(percentage[1] - percentage[0])+'%;'"></div>
            <div class="handle" @mousedown="down($event, 'min')" :style="'left: '+percentage[0]+'%;'"></div>
            <div class="handle" @mousedown="down($event, 'max')" :style="'left: '+percentage[1]+'%;'"></div>
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
            value: {
                type: Array,
                default: [0, 100],
            }
        },

        data() {
            return {
                value_: [0, 100],
                percentage: [0, 100],
                isGrabbing: false,
            }
        },

        mounted() {
            this.calculate(this.value ? this.value : this.value_)

            window.addEventListener('mouseup', () => {
                this.up()
            })

            window.addEventListener('mousemove', (event) => {
                this.move(event)
            })
        },

        watch: {
            value() {
                if (!this.isGrabbing) this.calculate(this.value ? this.value : this.value_)
            }
        },

        methods: {
            calculate(value) {
                if (typeof value !== 'object' || value.length !== 2)
                {
                    return
                }

                // TODO: clean up this mess
                if (this.isGrabbing === 'min')
                {
                    this.value_[0] = this.clamp(value[0], this.min, this.max)
                    this.value_[1] = this.clamp(value[1], this.value_[0], this.max)
                }
                else
                {
                    this.value_[1] = this.clamp(value[1], this.min, this.max)
                    this.value_[0] = this.clamp(value[0], this.min, this.value_[1])
                }

                this.value_[0] = this.value_[0] - this.value_[0] % this.step
                this.value_[1] = this.value_[1] - this.value_[1] % this.step

                this.percentage[0] = this.clamp((this.value_[0] - this.min) / (this.max - this.min) * 100, 0, 100)
                this.percentage[1] = this.clamp((this.value_[1] - this.min) / (this.max - this.min) * 100, 0, 100)
                this.$forceUpdate()

            },

            clamp(value, min, max) {
                return value <= min ? min : value >= max ? max : value;
            },



            down(event, handle = 'max') {
                this.isGrabbing = handle

                let max = this.$refs.bar.clientWidth
                let pos = event.clientX - this.$refs.bar.getBoundingClientRect().left

                let percentage = this.clamp(pos / max, 0, 1)
                let value = (this.max - this.min) * percentage + this.min

                value = (this.isGrabbing === 'min') ? [value, this.value_[1]] : [this.value_[0], value]
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

                value = (this.isGrabbing === 'min') ? [value, this.value_[1]] : [this.value_[0], value]
                this.calculate(value)
                this.emit()
            },

            up() {
                this.isGrabbing = false
            },

            emit(value = this.value_) {
                this.$emit('input', value)
                this.$emit('min', value[0])
                this.$emit('max', value[1])
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
        user-select: none

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
                pointer-events: all
</style>