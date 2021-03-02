<template>
    <div class="ccc-container block">
        <div class="wrapper">
            <ui-select-input class="viewport-input" label="Viewport" no-border v-model="viewport" :options="[{'1080p':'1920 x 1080'}, {'720p':'1280 x 720'}]"></ui-select-input>
            <ui-text-input class="url-input" label="URL" no-border v-model="url"></ui-text-input>
            <ui-button class="submit-button" :loading="loading" icon="&#983881;" @click="$store.dispatch('createReport', url)">Analyse</ui-button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                viewport: null,
                url: 'https://fireship.io/',
            }
        },

        computed: {
            loading() {
                return this.$store.getters.reportScanning
            }
        },

        components: {},
    }
</script>

<style lang="sass" scoped>
    .ccc-container
        .wrapper
            position: relative
            display: flex
            align-items: center
            margin: 15px 0
            border-radius: 5px
            border: var(--input-border)

            .viewport-input
                width: 170px

            .url-input
                flex: 1

            .submit-button
                margin: 5px
</style>