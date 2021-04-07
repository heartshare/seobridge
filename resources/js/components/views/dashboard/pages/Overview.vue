<template>
    <div class="page-container limiter">
        <h1>Overview</h1>
        <div class="sheet padding">
            <p style="margin-top: 0px"><b>Create or join a team here:</b></p>
            <ui-button icon="&#985740;" icon-left text border @click="$store.dispatch('setPage', 'teams')">My Team Page</ui-button>
        </div><br>
        <div class="sheet padding">
            <p style="margin-top: 0px"><b>Create your first report here:</b></p>
            <ui-button icon="&#988493;" icon-left text border @click="$store.dispatch('setPage', 'reports')">Reports Page</ui-button>
        </div>
    </div>
</template>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>