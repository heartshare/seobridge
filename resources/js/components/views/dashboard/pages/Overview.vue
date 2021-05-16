<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>Overview</h1>
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="limiter overlap">
            <div class="sheet padding">
                <h2>Get Started</h2>
                <p style="margin-top: 0px"><b>Create or join a team here:</b></p>
                <ui-button icon="&#985740;" icon-left @click="$store.dispatch('setPage', 'teams')">My Team Page</ui-button><br><br>
                <p><b>Create your first report here:</b></p>
                <ui-button icon="&#988493;" icon-left @click="$store.dispatch('setPage', 'reports')">Reports Page</ui-button><br>
            </div>
        </div>
    </div>
</template>

<style lang="sass" scoped>
    .page-container
        width: 100%
        display: inline-block !important
</style>