<template>
    <div class="subscription-plan-wrapper">
        <form class="subscription-form" method="POST" action="/go-pro/complete" @submit.prevent>
            <ui-text-input id="name-input" label="Full name" ac="full name"></ui-text-input>
            <ui-text-input id="street-input" label="Street" ac="street"></ui-text-input>
            <ui-text-input id="apt-input" label="Apt / Suite / Other" ac="street 2"></ui-text-input>
            <ui-text-input id="city-input" label="City" ac="city"></ui-text-input>
            <ui-select-input id="country-input" label="Country" ac="country" :options="countryOptions"></ui-select-input>
            <ui-text-input id="zip-input" label="Zip code" ac="zip"></ui-text-input>
            <ui-text-input id="card-holder-name-input" label="Card holder name"></ui-text-input>
            <span class="block" id="card-input"></span>
        </form>
        <div class="subscription-info">
            <div class="plan-header">
                <img :src="plan.image" :alt="plan.name + ' subscription plan image'" class="plan-image">
            </div>
            <div class="plan-details">
                <h4>{{plan.name}}</h4>
                <p>{{plan.description}}</p>
                <p>Price: <b>{{plan.cost | price}} per month</b></p>
            </div>
                
            <div class="plan-button">
                <ui-button :loading="subscriptionCreate.loading" @click="addCreditCard()">Subscribe for {{plan.cost | price}} / month</ui-button>
            </div>
        </div>
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
        props: {
            secret: String,
            plan: Object,
        },

        data() {
            return {
                countryOptions: [
                    {value: 'DE', label: 'Germany'},
                    {value: 'UK', label: 'United Kingdom'},
                    {value: 'US', label: 'United States of America'},
                ],

                subscriptionCreate: {
                    loading: false,
                },
            }
        },

        mounted() {
            cardElement.mount('#card-input')
        },

        filters: {
            price(price) {
                if (!price)
                {
                    return null
                }
                
                return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'EUR' }).format(price / 100)
            },
        },

        methods: {
            async addCreditCard() {
                this.subscriptionCreate.loading = true

                const { setupIntent, error } = await stripe.confirmCardSetup(this.secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: 'Card Holder Name' }
                    }
                })
    
                if (error)
                {
                    console.log(error.message)
                }
                else
                {
                    axios.post('/auth/go-pro/complete', {
                        paymentMethodId: setupIntent.payment_method,
                        planId: this.plan.id,
                    })
                    .then(response => {
                        console.log(response.data)
                        this.subscriptionCreate.loading = false
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.subscriptionCreate.loading = false
                    })
                }
            },
        },
    }
</script>

<style lang="sass" scoped>
    .subscription-plan-wrapper
        width: 100%
        display: flex
        gap: 30px
        padding: 100px 0

        .subscription-form
            display: grid
            flex: 2
            gap: 20px 15px
            grid-template-columns: repeat(10, 1fr)
            grid-template-rows: 50px 50px 50px 20px 50px 50px

            #name-input
                grid-column: 10 span
                grid-row: 1

            #street-input
                grid-column: 6 span
                grid-row: 2

            #apt-input
                grid-column: 4 span
                grid-row: 2

            #city-input
                grid-column: 4 span
                grid-row: 3

            #country-input
                grid-column: 4 span
                grid-row: 3

            #zip-input
                grid-column: 2 span
                grid-row: 3

            #card-holder-name-input
                grid-column: 10 span
                grid-row: 5

            #card-input
                grid-column: 10 span
                grid-row: 6
                border: var(--border)
                border-radius: 5px
                padding: 15px
                width: 100%
                height: 50px

        .subscription-info
            flex: 1
            display: flex
            flex-direction: column
            background: white
            filter: var(--elevation-1)
            border-radius: 10px

            .plan-header
                width: 100%
                border-radius: 10px 10px 0 0
                background: var(--primary)
                height: 100px
                position: relative
                background-image: url('/images/static/assets/terrain_white.svg')
                background-size: 1200px
                background-position: center
                margin-bottom: 40px

                .plan-image
                    height: 80px
                    width: 80px
                    object-fit: cover
                    border-radius: 100%
                    border: 3px solid white
                    background: white
                    position: absolute
                    bottom: 0
                    left: 50%
                    transform: translate(-50%, 50%)

            .plan-details
                padding: 15px
                display: flex
                gap: 15px
                flex-direction: column
                font-size: var(--text-size)

                h4
                    margin: 0
                    text-align: center

                p
                    margin: 0

            .plan-button
                padding: 15px
                border-top: var(--border)
                text-align: right
</style>