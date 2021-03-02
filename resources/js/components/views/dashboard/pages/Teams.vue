<template>
    <div class="page-container limiter">
        <h1>My Teams</h1>
        <fieldset v-for="team in teams" :key="team.id">
            <legend>{{team.name}}</legend>
            <p>
                Category: <b>{{team.category}}</b><br>
                Description: <b>{{team.description || '---'}}</b>
            </p>
            <p v-for="member in team.members" :key="team.id+'_'+member.id">{{member.id}}</p>
            <ui-icon-button @click="openTeamEditor(team)">&#984043;</ui-icon-button>
            <ui-icon-button @click="openTeamInviteDialog(team)">&#983060;</ui-icon-button>
            <ui-icon-button error @click="openTeamDeletionDialog(team)">&#985721;</ui-icon-button>
        </fieldset><br>

        <fieldset>
            <legend>Add Teams</legend>
            <ui-button icon="&#984085;" @click="openTeamEditor()">New Team</ui-button>
        </fieldset>



        <ui-option-dialog ref="teamEditDialog" @close="resetTeamEditor()">
            <template v-slot:heading>
                Create or edit your team
            </template>

            <template v-slot:inputs>
                <ui-text-input label="Team name" v-model="teamEdit.name"></ui-text-input>
                <ui-select-input label="Team category" v-model="teamEdit.category" :options="teamCategories"></ui-select-input>
                <ui-textarea label="Team description" class="team-description-input" :max="1000" show-max v-model="teamEdit.description"></ui-textarea>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamEditor()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#983443;" @click="saveTeam()">Save</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamDeletionDialog" @close="resetTeamDeletion()">
            <template v-slot:heading>
                Delete: <b>{{teamDelete.name}}</b>
            </template>

            <span>
                Do you want to permanently delete your team: <b>{{teamDelete.name}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamDeletion()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#985721;" @click="deleteTeam()">Delete Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamInviteDialog" @close="resetTeamInvite()">
            <template v-slot:heading>
                Add team member to: <b>{{teamInvite.name}}</b>
            </template>

            <template v-slot:inputs>
                <ui-text-input label="Invite via email" v-model="teamInvite.inviteName"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamInvite()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#984202;" @click="sendTeamInvitation()">Send Invitation</ui-button>
            </template>
        </ui-option-dialog>
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

                teamInvite: {
                    id: null,
                    name: '',
                    inviteName: '',
                    loading: false,
                },

                teamDelete: {
                    name: '',
                    id: null,
                    loading: false,
                }
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

                this.$refs.teamEditDialog.open()
            },

            resetTeamEditor() {
                this.teamEdit.id = null
                this.teamEdit.name = ''
                this.teamEdit.description = ''
                this.teamEdit.category = ''
                this.$refs.teamEditDialog.close()
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



            openTeamDeletionDialog(team) {
                this.teamDelete.id = team.id
                this.teamDelete.name = team.name
                this.$refs.teamDeletionDialog.open()
            },

            resetTeamDeletion() {
                this.teamDelete.id = null
                this.teamDelete.name = ''
                this.$refs.teamDeletionDialog.close()
            },

            deleteTeam(team = this.teamDelete) {
                this.teamDelete.loading = true
                
                axios.post('/auth/team/delete-team', {
                    id: team.id,
                    name: team.name,
                })
                .then(response => {
                    this.$store.commit('deleteTeam', response.data)
                    this.teamDelete.loading = false
                    this.resetTeamDeletion()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamDelete.loading = false
                })
            },



            openTeamInviteDialog(team) {
                this.teamInvite.id = team.id
                this.teamInvite.name = team.name
                this.$refs.teamInviteDialog.open()
            },

            resetTeamInvite() {
                this.teamInvite.id = null
                this.teamInvite.name = ''
                this.teamInvite.inviteName = ''
                this.$refs.teamInviteDialog.close()
            },

            sendTeamInvitation() {
                this.teamInvite.loading = true
                
                axios.post('/auth/team/create-invite', {
                    teamId: this.teamInvite.id,
                    inviteName: this.teamInvite.inviteName,
                })
                .then(response => {
                    console.log(response.data)
                    this.teamInvite.loading = false
                    this.resetTeamInvite()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamInvite.loading = false
                })
            },
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .team-description-input
            resize: none
            height: 150px
</style>