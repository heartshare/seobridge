<template>
    <div class="page-container limiter">
        <transition-group name="slide" class="block">
            <div class="team-wrapper" v-for="team in teams" :key="team.id">
                <div class="team-header">
                    <div class="title">{{team.name}}</div>

                    <ui-popover-menu>
                        <template v-slot:trigger>
                            <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                        </template>

                        <ui-menu-item icon="&#984043;" @click="openTeamEditor(team)">Edit Team</ui-menu-item>
                        <ui-menu-item icon="&#983060;" @click="openTeamInviteDialog(team)">Add Member</ui-menu-item>
                        <ui-menu-divider></ui-menu-divider>
                        <ui-menu-item icon="&#985721;" @click="openTeamDeletionDialog(team)">Delete Team</ui-menu-item>
                    </ui-popover-menu>
                </div>

                <div class="team-content">
                    <div class="member-card" v-for="member in team.members" :key="member.id">
                        <div class="background">
                            <ui-popover-menu class="more-button">
                                <template v-slot:trigger>
                                    <ui-icon-button>&#983513;</ui-icon-button>
                                </template>

                                <ui-menu-item icon="&#984043;">Edit Member</ui-menu-item>
                                <ui-menu-item icon="&#983213;">Remove Member</ui-menu-item>
                            </ui-popover-menu>
                        </div>

                        <img src="/images/defaults/default_profile_image.svg" class="profile-image">

                        <div class="name">{{member.user.username}}</div>
                        
                    </div>
                    <p>
                        Category: <b>{{team.category}}</b><br>
                        Description: <b>{{team.description || '---'}}</b>
                    </p>
                </div>
            </div>
        </transition-group>



        <button class="fab" @click="openTeamEditor()">&#984085;</button>



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

        .fab
            height: 56px
            width: 56px
            font-family: 'Material Icons'
            color: white
            background: var(--primary)
            display: grid
            place-content: center
            font-size: 24px
            position: fixed
            bottom: 30px
            right: 30px
            border-radius: 100%
            border: none
            filter: var(--elevation-2)
            cursor: pointer
            user-select: none
            transition: all 200ms
            z-index: 100

            &:hover
                filter: var(--elevation-4)

        .team-description-input
            resize: none
            height: 150px

        .team-wrapper
            width: 100%
            display: inline-flex
            flex-direction: column
            background: white
            border-radius: 7px
            filter: var(--elevation-2)
            margin: 15px 0
            transition: all 300ms

            &.slide-enter
                transform: translateY(-100px)
                opacity: 0

            &.slide-leave-to
                transform: scale(0)
                opacity: 0

            &.slide-leave-active
                position: absolute

            .team-header
                display: flex
                align-items: center
                padding: 5px 0
                border-radius: 7px 7px 0 0

                .title
                    flex: 1
                    font-size: 16px
                    line-height: 20px
                    font-weight: 600
                    text-transform: uppercase
                    color: var(--heading-gray)
                    padding: 0 15px

                .timestamp
                    line-height: 20px
                    font-size: var(--text-size)
                    color: var(--text-gray)

                .more-button
                    margin: 0 5px

            .team-content
                display: flex
                gap: 15px
                padding: 0 15px 15px
                position: relative

                .member-card
                    border-radius: 5px
                    border: var(--border)
                    width: 170px
                    height: 220px
                    position: relative

                    .name
                        width: 100%
                        font-size: var(--text-size)
                        color: var(--heading-gray)
                        font-weight: 600
                        height: 40px
                        display: grid
                        place-content: center

                    .more-button
                        position: absolute
                        top: 0
                        right: 0
                        padding: 5px
                        display: block !important

                    .background
                        height: 80px
                        width: 100%
                        border-bottom: var(--border)
                        background: var(--bg)
                        background-image: url('/images/app/dashboard/pattern.svg')
                        background-size: 1000px
                        background-position: top
                        background-repeat: no-repeat
                        border-radius: 5px 5px 0 0
                        display: block

                    .profile-image
                        height: 80px
                        width: 80px
                        object-fit: cover
                        border-radius: 100%
                        margin: -40px auto 5px
                        display: block
                        padding: 5px
                        background: var(--bg)
                        font-size: 15px
</style>