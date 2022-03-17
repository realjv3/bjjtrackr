<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="memberships-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/lineup.png"
                >
                    <v-card-title>How to manage membership plans and members</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Create membership plans:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemMemberships.png"
                                alt="Membership Plans"
                            />
                            menu item in the left side navigation menu to see your membership plans.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the <i>Add Membership Plan</i> button
                            <v-img
                                class="d-inline-block mx-2"
                                width="193px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/buttonAddMembership.png"
                                alt="buttonAddMembership"
                            />
                            to create a new membership plan that your students can subscribe to.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form with a name, price and terms for the membership plan, then hit save.
                            <v-img
                                height="476px"
                                width="382px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalAddMembership.png"
                                alt="Membership Plan"
                            /><br/>
                            The newly created membership plan will appear in the table. Hit the pencil icon to edit it.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-4">
                            Membership plans and their prices can be set to inactive. Membership plans can have more than one price.
                            <v-img
                                height="749px"
                                width="382px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalEditMembership.png"
                                alt="Membership Plan"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To subscribe a student to a membership plan:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemMemberships.png"
                                alt="Memberships"
                            />
                            menu item in the left side navigation menu to see your members.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the <i>Add Member</i> button
                            <v-img
                                class="d-inline-block mx-2"
                                width="117px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/buttonAddMember.png"
                                alt="buttonAddMember"
                            />
                            to subscribe a student to a membership plan.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form and hit save.
                            <v-img
                                height="448px"
                                width="382px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalAddMember.png"
                                alt="Membership Plan"
                            /><br/>
                            Once the membership has been created, you can pause, cancel, or resume the membership using
                            the icon buttons in the <i>Memberships</i> table.
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>
<script>
export default {
    name: 'HelpPeople',
    components: {}

}
</script>
