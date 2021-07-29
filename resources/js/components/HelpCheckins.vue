<template>
    <v-lazy>
        <v-row justify="center" class="ma-16">
            <v-card id="people-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://res.cloudinary.com/realjv3/image/upload/v1627334598/flowrolled/wornBlueBelt.jpg"
                >
                    <v-card-title>How to track attendance</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Students can check-in via input or QR code. Monitor progress reports in the Reports section.<br/>
                    To checkin students:
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
                            Then press the 'Checkin' button.
                            <v-img
                                width="107px"
                                class="ma-2"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627337371/flowrolled/speedDialCheckin.png"
                                alt="Checkin"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form and hit save.
                            <v-img
                                class="ma-2"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627338093/flowrolled/ModalCheckinNew.png"
                                alt="Checkin"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-4">
                            Students can also scan their QR code to check-in.
                            <v-img
                                width="164px"
                                class="ma-2"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627396549/flowrolled/menuItemQR.png"
                                alt="QR Codes"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To view, update, or delete checkins:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627387398/flowrolled/menuItemCheckins.png"
                                alt="Checkins"
                            />
                            menu item in the left side navigation menu to see student checkins.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the pencil icon
                            <v-img
                                class="d-inline-block mx-2"
                                width="66px"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627388099/flowrolled/actionButtons.png"
                                alt="pencil"
                            />
                            on the checkin that you want to edit, or hit the trash can icon to delete.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Edit the form and hit save.
                            <v-img
                                class="ma-2"
                                src="https://res.cloudinary.com/realjv3/image/upload/v1627396154/flowrolled/ModalCheckinNew.png"
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
    name: 'HelpCheckins'
}
</script>
