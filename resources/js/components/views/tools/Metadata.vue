<template>
    <div class="tool-container">
        <div class="tool-input-container">
            <p class="text">
                <b>Get the metadata of your website.</b>
            </p>
            <ui-button class="submit-button" :loading="isLoading" icon="&#983881;" @click="getMetadata()">Fetch now</ui-button>
            <ui-textarea class="input-box" label="Website URLs (one per line)" v-model="urlInput"></ui-textarea>
        </div>

        <div class="no-result-container" v-show="Object.keys(fetchedData).length === 0">
            <div class="icon">&#987653;</div>
            <div class="text">No Results... yet</div>
        </div>

        <metadata-result v-for="(urlData, url) in fetchedData" :key="url" :data="urlData" :href="url"></metadata-result>
    </div>
</template>

<script>
    import MetadataResult from './components/MetadataResult'

    export default {
        data() {
            return {
                urlInput: 'https://freuwort.com\nhttps://fireship.io',
                fetchedData: {},
                isLoading: false,
            }
        },

        methods: {
            getMetadata() {
                this.isLoading = true

                let urls = this.urlInput.split('\n')

                axios.post('/get-meta-data', {urls})
                .then(response => {
                    this.fetchedData = response.data
                    console.log(response.data)
                    this.isLoading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.isLoading = false
                })
            }
        },

        components: {
            MetadataResult,
        }
    }
</script>

<style lang="sass" scoped>
    .tool-container
        width: 100%

        .tool-input-container
            width: 100%
            display: grid
            grid-template-rows: 40px auto
            grid-auto-columns: 1fr auto
            grid-template-areas: "text button" "input input"
            gap: 20px 0
            margin: 20px 0

            .submit-button
                grid-area: button

            .text
                grid-area: text
                margin: 0
                align-self: center

            .input-box
                grid-area: input
                resize: none
                height: 200px

        .no-result-container
            background: var(--bg-dark)
            border-radius: 10px
            display: flex
            flex-direction: column
            align-items: center
            padding: 40px 20px
            user-select: none
            gap: 10px

            .text
                text-transform: uppercase
                font-size: 12px
                letter-spacing: 2px
                font-weight: 700
                user-select: none

            .icon
                font-family: 'Material Icons'
                font-size: 60px
                line-height: 80px
                opacity: 0.8
                user-select: none
</style>