<template>
    <div class="page-container limiter">
        <h1>Settings</h1>

        <fieldset>
            <legend>Account settings</legend>

            <ui-button icon="&#984421;" :loading="passwordChange.loading" @click="openPasswordChangeDialog()">Change Password</ui-button><br><br>
            <ui-button icon="&#983394;" text border>Request Data (WIP)</ui-button>
        </fieldset><br>

        <fieldset>
            <legend>Subscriptions</legend>
            <ui-button icon="&#984694;" text border :loading="setupIntent.loading" @click="getSetupIntent()">Add Credit Card</ui-button>
            <ui-button href="/auth/subscriptions/billing-portal" icon="&#984012;" text>Billing Portal</ui-button>

            <p>
                <span>
                    <ui-text-input label="Card holder name" v-model="setupIntent.cardHolderName"></ui-text-input>

                    <div id="card-element"></div>
                    
                    <ui-button @click="addCreditCard()">Add Credit Card</ui-button>
                </span>
            </p>
            <p>
                <ui-button @click="createSubscription()">Test Subscription</ui-button>
            </p>
        </fieldset><br>

        <fieldset>
            <legend>Close Account</legend>
            <ui-button error :loading="accountClose.loading" @click="openAccountCloseDialog()">Close your account</ui-button>
        </fieldset>

        <ui-option-dialog ref="passwordChangeDialog" @close="resetPasswordChange()">
            <template v-slot:heading>
                Change your password
            </template>

            <template v-slot:inputs>
                <ui-password-input label="Current Password" v-model="passwordChange.current"></ui-password-input>
                <ui-password-input label="New Password" ac="new-password" rating v-model="passwordChange.new"></ui-password-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetPasswordChange()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button :loading="passwordChange.loading" :disabled="passwordChange.current.length < 8 || passwordChange.new.length < 8" @click="changePassword()">Change Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="accountCloseDialog" @close="resetAccountClose()">
            <template v-slot:heading>
                Close your account
            </template>

            <span>
                Do you want to permanently close your account?
            </span>

            <template v-slot:inputs>
                <ui-password-input label="Current Password" v-model="accountClose.password"></ui-password-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetAccountClose()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error :loading="accountClose.loading" :disabled="accountClose.password.length < 8" @click="closeAccount()">Close Now</ui-button>
            </template>
        </ui-option-dialog>
    </div>
</template>

<script>
    const stripe = Stripe('pk_test_51IPwiCDa1TGHitv5NwiZUsCe9Yy28YRgBjzXUVoFSs5eqQnRgUeXoUNO6hEl3CXWWq63E954U8Cw3nt0vSo3Yx8C0089OD4j7Z')

    const elements = stripe.elements()
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                fontSize: '15px',
                color: '#2F3542',
                '::placeholder': {
                    color: '#666F79',
                },
            },
        },
    })
    
    export default {
        data() {
            return {
                passwordChange: {
                    current: '',
                    new: '',
                    loading: false,
                },

                accountClose: {
                    password: '',
                    loading: false,
                },

                setupIntent: {
                    intent: null,
                    cardHolderName: '',
                    loading: false,
                },
            }
        },

        mounted() {
            cardElement.mount('#card-element')
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        methods: {
            openPasswordChangeDialog() {
                this.$refs.passwordChangeDialog.open()
            },

            resetPasswordChange() {
                this.passwordChange.current = ''
                this.passwordChange.new = ''
                this.$refs.passwordChangeDialog.close()
            },

            changePassword() {
                this.passwordChange.loading = true

                axios.post('/auth/user/change-password', {
                    password: this.passwordChange.current,
                    newPassword: this.passwordChange.new,
                })
                .then(response => {
                    setTimeout(() => {
                        this.resetPasswordChange()
                        this.passwordChange.loading = false
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.passwordChange.loading = false
                })
            },



            openAccountCloseDialog() {
                this.$refs.accountCloseDialog.open()
            },

            resetAccountClose() {
                this.accountClose.password = ''
                this.$refs.accountCloseDialog.close()
            },

            closeAccount() {
                this.accountClose.loading = true

                axios.post('/auth/user/close-account', {
                    password: this.accountClose.password,
                })
                .then(response => {
                    setTimeout(() => {
                        this.resetAccountClose()
                        this.accountClose.loading = false
                        this.$store.dispatch('logout', ()=>{
                            window.location = '/'
                        })
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.accountClose.loading = false
                })
            },



            getSetupIntent() {
                this.setupIntent.loading = true

                axios.post('/auth/subscriptions/get-setup-intent')
                .then(response => {
                    setTimeout(() => {
                        this.setupIntent.intent = response.data
                        this.setupIntent.loading = false
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.setupIntent.loading = false
                })
            },

            async addCreditCard() {
                const { setupIntent, error } = await stripe.confirmCardSetup(this.setupIntent.intent.client_secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: this.setupIntent.cardHolderName }
                    }
                })

                if (error)
                {
                    console.log(error.message)
                }
                else
                {
                    console.log('CARD ADDED!')
                }
            },



            createSubscription() {
                axios.post('/auth/subscriptions/create-test-subscription')
                .then(response => {
                    console.log(response.data)
                    console.log('YAY! IT WORKED!')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        #card-element
            border: var(--border)
            border-radius: 5px
            padding: 15px
            width: 100%
</style>