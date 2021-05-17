<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>Social Media Manager</h1>
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="limiter overlap">
            <div class="sheet">
                <ui-tabs-header :tabs="{'overview': 'Overview', 'planner': 'Post Planner', 'analytics': 'Analytics'}" v-model="tab"></ui-tabs-header>
                <p>
                    <ui-button>Normal</ui-button>
                    <ui-button disabled>Disabled</ui-button>
                    <ui-button loading>loading</ui-button><br>

                    <ui-button text>Normal</ui-button>
                    <ui-button text disabled>Disabled</ui-button>
                    <ui-button text loading>loading</ui-button><br>

                    <ui-button text border>Normal</ui-button>
                    <ui-button text border disabled>Disabled</ui-button>
                    <ui-button text border loading>loading</ui-button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tab: 'overview',
            }
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>