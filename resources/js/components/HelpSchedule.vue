<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="schedule-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/wallCalendar.jpg"
                >
                    <v-card-title>How to manage the class schedule</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    It's good to have a class schedule up on the schedule page. That way students can check into specific classes.
                    <br/>
                    To put a class on the schedule:
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
                            Then press the 'Class' button.
                            <v-img
                                height="167px"
                                width="107px"
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/speedDialClass.png"
                                alt="Class"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form and hit save.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalClassNew.png"
                                alt="Class"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To view, update, or delete scheduled classes:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemSchedule.png"
                                alt="Schedule"
                            />
                            menu item in the left side navigation menu to see your schedule.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Click on the scheduled class
                            <v-img
                                class="d-inline-block mx-2"
                                width="113px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/class.png"
                                alt="class"
                            />
                            that you want to edit to open it.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Press delete to remove class the class, otherwise edit the form and hit save.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalClassEdit.png"
                                alt="Class"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>
<script>
export default {
    name: 'HelpSchedule'
}
</script>
