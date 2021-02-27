<template>
    <div class="page-container limiter">
        <h1>My Profile</h1>

        <form @submit.stop.prevent>
            <p>
                <ui-text-input label="Firstname" ac="firstname" v-model="nameChange.firstname"></ui-text-input>
                <ui-text-input label="Lastname" ac="lastname" v-model="nameChange.lastname"></ui-text-input>
                <ui-button @click="changeName()" :loading="nameChange.loading">Change Name</ui-button>
            </p>
        </form>
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
</style>