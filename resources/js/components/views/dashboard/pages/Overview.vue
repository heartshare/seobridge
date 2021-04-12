<template>
    <div class="page-container limiter">
        <h1>Overview</h1>
        <div class="sheet padding">
            <p style="margin-top: 0px"><b>Create or join a team here:</b></p>
            <ui-button icon="&#985740;" icon-left @click="$store.dispatch('setPage', 'teams')">My Team Page</ui-button><br><br>
            <p><b>Create your first report here:</b></p>
            <ui-button icon="&#988493;" icon-left @click="$store.dispatch('setPage', 'reports')">Reports Page</ui-button>
        </div><br>

        <div class="sheet padding">
            <h3 style="margin: 0px">The On-Page SEO Process</h3>
            <p>
                On-Page search engine optimizing can broken down in 4 steps:<br>
            </p>
            <p>
                <b>1) Keyword generation</b> – Generating a set of keywords to use for your business or website<br>
                <b>2) Traffic estimation</b> – Estimating the traffic that can be expected from any given keyword<br>
                <b>3) Keyword difficulty estimation</b> – Checking the competitiveness of any given keyword<br>
                <b>4) Content creation / monitoring</b> – Creating or optimizing content around the keywords and optimizing technical aspects
            </p>
            <h4>SEO Bridge tools</h4>
            <p>
                SEO Bridge plans to provide you with the tools needed to cover the entire workflow from step 1 to 4.<br>
                Down below you can see the current development status for each tools:
            </p>
            <p>
                <b>Keyword Generator:</b><br>
                <span style="color: var(--warning)">planned</span>
            </p>
            <p>
                <b>Keyword Analyzer / Traffic Estimator:</b><br>
                <span style="color: var(--warning)">planned</span>
            </p>
            <p>
                <b>Structure Analyzer:</b><br>
                <span style="color: var(--success)">available now</span><br>
                <ui-button style="margin: 5px 0" text border @click="$store.dispatch('setPage', 'reports')">Reports Tool</ui-button>
            </p>
        </div>

    </div>
</template>

<style lang="sass" scoped>
    .page-container
        width: 100%
        display: inline-block !important
</style>