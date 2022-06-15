<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="people-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/kidsClass.jpg"
                >
                    <v-card-title>How to manage student, administrator, and instructor records</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Input people with various roles in your organization. Once a person record is created, they can login
                    to the app.<br/>
                    <b>Administrators</b> have full access to the account, <b>instructors</b> can only check students in,
                    and <b>students</b> can only see their own checkins and the class schedule.
                    To create a student, instructor, admin, or a wearer of many hats in your gym:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-btn fab dark small color="secondary" class="mx-2">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                            icon on the bottom-right of the page.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Then press the 'Person' button.
                            <v-img
                                height="167px"
                                width="107px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/speedDialPerson.png"
                                alt="Person"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form and hit save.
                            <v-img
                                height="380px"
                                width="382px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/ModalNewPerson.png"
                                alt="Person"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To view, update, or delete people from your organization:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemPeople.png"
                                alt="People"
                            />
                            menu item in the left side navigation menu to see your people.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the pencil icon
                            <v-img
                                class="d-inline-block mx-2"
                                width="66px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/actionButtons.png"
                                alt="pencil"
                            />
                            on the person's record that you want to edit, or hit the trash can icon to delete.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">Fill out the form and hit save.</v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>
<script>
export default {
    name: 'HelpPeople'
}
</script>
