<template>
    <div class="page-container limiter">

        <div class="sheet">
            <ui-tabs-header :tabs="{'general': 'General', 'profile': 'Profile', 'subscriptions': 'Subscriptions', 'security': 'Security'}" v-model="tab"></ui-tabs-header>

            <div class="tab-box" v-show="tab === 'general'">
                <div class="row-wrapper">
                    <b>Close your account</b>
                    <div class="spacer"></div>
                    <ui-button small error :loading="accountClose.loading" @click="openAccountCloseDialog()">Close</ui-button>
                </div>
            </div>

            <div class="tab-box" v-show="tab === 'profile'">
                <form @submit.stop.prevent>
                    <div class="row-wrapper border">
                        <b>Change Your Name</b>
                        <div class="spacer"></div>
                        <ui-button small class="submit-button" @click="changeName()" :disabled="(nameChange.firstname || '') + (nameChange.lastname || '') === nameChange.legacy" :loading="nameChange.loading">Save</ui-button>
                    </div>

                    <div class="row-wrapper">
                        <ui-text-input label="Firstname" ac="firstname" v-model="nameChange.firstname"></ui-text-input>
                        <ui-text-input label="Lastname" ac="lastname" v-model="nameChange.lastname"></ui-text-input>
                    </div>
                </form>
            </div>

            <div class="tab-box" v-show="tab === 'security'">
                <div class="row-wrapper border">
                    <b>Change Password</b>
                    <div class="spacer"></div>
                    <ui-button small :loading="passwordChange.loading" @click="openPasswordChangeDialog()">Edit</ui-button>
                </div>
                
                <div class="row-wrapper border">
                    <b>Multi Factor Authentication</b>
                    <div class="spacer"></div>
                    <ui-switch style="margin: 5px 0" :value="user.is_mfa_enabled"></ui-switch>
                </div>

                <div class="row-wrapper">
                    <span>App Authentication</span>
                    <div class="spacer"></div>
                    <ui-button small text border @click="setupTOTPMFA()">Setup</ui-button>
                    <ui-icon-button>&#983360;</ui-icon-button>
                </div>

                <div class="row-wrapper">
                    <span>SMS Authentication (coming soon)</span>
                    <div class="spacer"></div>
                    <ui-button small text border disabled>Setup</ui-button>
                    <ui-icon-button>&#983360;</ui-icon-button>
                </div>

                <div class="row-wrapper">
                    <span>Security Key (coming soon)</span>
                    <div class="spacer"></div>
                    <ui-button small text border disabled>Setup</ui-button>
                    <ui-icon-button>&#983360;</ui-icon-button>
                </div>


                <qr-code :value="TOTPMFASetup.url" :options="{width: 200}"></qr-code>

                <ui-text-input label="Passnumber" v-model="TOTPMFASetup.TOTPInput"></ui-text-input>

                <ui-button @click="verifyTOTPMFA()">Verify</ui-button>
            </div>

            <div class="tab-box" v-show="tab === 'subscriptions'">
            </div>
        </div>

        <!-- <fieldset>
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
        </fieldset><br> -->

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
    // const stripe = Stripe('pk_test_51IPwiCDa1TGHitv5NwiZUsCe9Yy28YRgBjzXUVoFSs5eqQnRgUeXoUNO6hEl3CXWWq63E954U8Cw3nt0vSo3Yx8C0089OD4j7Z')

    // const elements = stripe.elements()
    // const cardElement = elements.create('card', {
    //     style: {
    //         base: {
    //             fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
    //             fontSize: '15px',
    //             color: '#2F3542',
    //             '::placeholder': {
    //                 color: '#666F79',
    //             },
    //         },
    //     },
    // })

    import VueQrcode from '@chenfengyuan/vue-qrcode';
    
    export default {
        data() {
            return {
                tab: 'general',

                nameChange: {
                    firstname: '',
                    lastname: '',
                    legacy: null,
                    loading: false,
                },

                passwordChange: {
                    current: '',
                    new: '',
                    loading: false,
                },

                accountClose: {
                    password: '',
                    loading: false,
                },

                TOTPMFASetup: {
                    url: null,
                    TOTPInput: '',
                },

                setupIntent: {
                    intent: null,
                    cardHolderName: '',
                    loading: false,
                },
            }
        },

        mounted() {
            // cardElement.mount('#card-element')
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        watch: {
            user: {
                handler() {
                    this.nameChange.firstname = this.user.firstname || ''
                    this.nameChange.lastname = this.user.lastname || ''
                    this.nameChange.legacy = this.nameChange.firstname + this.nameChange.lastname
                },
                immediate: true,
            }
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



            changeName() {
                this.nameChange.loading = true

                axios.post('/auth/user/change-name', {
                    firstname: this.nameChange.firstname,
                    lastname: this.nameChange.lastname,
                })
                .then(response => {
                    this.$store.commit('userInfo', {
                        firstname: response.data.firstname,
                        lastname: response.data.lastname,
                    })

                    this.nameChange.legacy = (response.data.firstname || '') + (response.data.lastname || '')

                    setTimeout(() => { this.nameChange.loading = false }, 500)
                })
                .catch(error => {
                    console.log(error.response)
                    this.nameChange.loading = false
                })
            },



            setupTOTPMFA() {
                axios.post('/auth/user/setup-totp-mfa')
                .then(response => {
                    console.log(response)
                    this.TOTPMFASetup.url = response.data
                })
                .catch(error => {
                    console.log(error.response)
                })
            },

            verifyTOTPMFA() {
                axios.post('/auth/user/verify-totp-mfa', {
                    secret: this.TOTPMFASetup.TOTPInput,
                })
                .then(response => {
                    console.log(response)
                })
                .catch(error => {
                    console.log(error.response)
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
        },

        components: {
            'qr-code': VueQrcode
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
        display: inline-block !important
        padding-top: 10px !important

        .tab-box
            padding: 15px
            display: block
            width: 100%

            .row-wrapper
                display: flex
                align-items: center
                padding: 10px 0
                gap: 10px
                
                &.border
                    border-bottom: var(--border)

                .spacer
                    flex: 1

        .name-wrapper
            background: var(--bg)
            border-radius: 5px
            position: relative
            display: flex
            width: 100%
            max-width: 600px
            
            .submit-button
                margin: 5px

            &::after
                content: ''
                height: 100%
                width: 100%
                position: absolute
                top: 0
                left: 0
                border-radius: 5px
                border: var(--input-border)
                pointer-events: none
                box-sizing: border-box

        #card-element
            border: var(--border)
            border-radius: 5px
            padding: 15px
            width: 100%
</style>