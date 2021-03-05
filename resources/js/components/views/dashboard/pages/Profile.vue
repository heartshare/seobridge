<template>
    <div class="page-container limiter">

        <div class="header">
            <div class="background-image"></div>
            <img src="/images/defaults/default_profile_image.svg" class="profile-image">
        
            <form @submit.stop.prevent>
                <p>
                    <ui-text-input label="Firstname" ac="firstname" v-model="nameChange.firstname"></ui-text-input>
                    <ui-text-input label="Lastname" ac="lastname" v-model="nameChange.lastname"></ui-text-input>
                    <ui-button @click="changeName()" :loading="nameChange.loading">Change Name</ui-button>
                </p>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                nameChange: {
                    firstname: '',
                    lastname: '',
                    loading: false,
                },
            }
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        watch: {
            user: {
                handler() {
                    this.nameChange.firstname = this.user.firstname
                    this.nameChange.lastname = this.user.lastname
                },
                immediate: true,
            }
        },

        methods: {
            changeName() {
                this.nameChange.loading = true

                axios.post('/auth/user/change-name', {
                    firstname: this.nameChange.firstname,
                    lastname: this.nameChange.lastname,
                })
                .then(response => {
                    this.$store.commit('userFirstname', response.data.firstname)
                    this.$store.commit('userLastname', response.data.lastname)

                    setTimeout(() => { this.nameChange.loading = false }, 500)
                })
                .catch(error => {
                    console.log(error.response)
                    this.nameChange.loading = false
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .header
            width: 100%
            background: var(--bg)
            filter: var(--elevation-2)
            border-radius: 7px
            margin-top: 15px

            .background-image
                height: 250px
                width: 100%
                background: var(--bg-dark)
                background-image: url('/images/app/dashboard/pattern.svg')
                background-size: cover
                background-position: center
                background-repeat: no-repeat
                border-radius: 7px 7px 0 0
                display: block

            .profile-image
                height: 140px
                width: 140px
                object-fit: cover
                border-radius: 100%
                margin: -70px auto 15px
                display: block
                padding: 5px
                background: var(--bg)
</style>