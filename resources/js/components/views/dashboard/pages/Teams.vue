<template>
    <div class="page-container limiter">
        <h1 v-tooltip.bottom-end="{content: '<b>TEST TOOLTIP</b><br>Dies ist ein ToolTip'}">My Teams</h1>
        <fieldset v-for="team in teams" :key="team.id">
            <legend>{{team.name}}</legend>
            <p>
                Category: <b>{{team.category}}</b><br>
                Description: <b>{{team.description || '---'}}</b>
            </p>
            <p v-for="member in team.members" :key="team.id+'_'+member.id">{{member.id}}</p>
            <ui-button icon="&#984043;" light @click="openTeamEditor(team)">Edit Team</ui-button>
            <ui-icon-button error>&#985721;</ui-icon-button>
        </fieldset>

        <p>
            <ui-button icon="&#984085;" @click="openTeamEditor()">New Team</ui-button>
        </p>

        <p>
            <ui-text-input label="Team name" v-model="teamEdit.name"></ui-text-input>
            <ui-select-input label="Team category" v-model="teamEdit.category" :options="teamCategories"></ui-select-input>
            <ui-textarea label="Team description" :max="1000" show-max v-model="teamEdit.description"></ui-textarea>
            <ui-button icon="&#983382;" light icon-left @click="resetTeamEditor()">close</ui-button>
            <ui-button icon="&#983443;" @click="saveTeam()">Save</ui-button>
        </p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                teamCategories: [
                    {'technology': 'Technology'},
                    {'marketing': 'Marketing'},
                    {'other': 'Other'},
                ],

                teamEdit: {
                    id: null,
                    name: '',
                    description: '',
                    category: '',
                    loading: true,
                },
            }
        },

        computed: {
            teams() {
                return this.$store.getters.teams
            }
        },

        methods: {
            openTeamEditor(team = null) {
                if (team)
                {
                    this.teamEdit.id = team.id
                    this.teamEdit.name = team.name
                    this.teamEdit.description = team.description
                    this.teamEdit.category = team.category
                }
                else
                {
                    this.resetTeamEditor()
                }
            },

            resetTeamEditor() {
                this.teamEdit.id = null
                this.teamEdit.name = ''
                this.teamEdit.description = ''
                this.teamEdit.category = ''
            },

            saveTeam() {
                this.teamEdit.loading = true
                
                axios.post('/auth/team/update-or-create-team', {
                    id: this.teamEdit.id,
                    name: this.teamEdit.name,
                    category: this.teamEdit.category,
                    description: this.teamEdit.description,
                })
                .then(response => {
                    this.$store.commit('setTeam', response.data)
                    this.teamEdit.loading = false
                    this.resetTeamEditor()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamEdit.loading = false
                })
            },
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>