<template>
    <div class="container">
        <p>
            <b>Get the metadata of your website.</b><br>
            Enter one url per line.
        </p>
        <ui-textarea label="Website URLs" v-model="urlInput"></ui-textarea>
        <ui-button class="submit-button" @click="getMetadata()">Get Metadata</ui-button><br><br>

        <fieldset v-for="(urlData, i) in fetchedData" :key="i">
            <legend>{{i}}</legend>
            <p>
                Open Graph Image: {{urlData['og:image']}}
            </p>
        </fieldset>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                urlInput: '',
                fetchedData: {},
            }
        },

        methods: {
            getMetadata() {
                let urls = this.urlInput.split('\n')

                axios.post('/get-meta-data', {urls})
                .then(response => {
                    this.fetchedData = response.data
                    console.log(response.data)
                })
                .catch(error => {
                    console.log(error.response)
                })
            }
        },
    }
</script>

<style lang="sass" scoped>
    .container
        width: 100%

        .submit-button
            margin-top: 20px
</style>