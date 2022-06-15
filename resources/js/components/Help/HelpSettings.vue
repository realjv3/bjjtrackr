<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="schedule-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/gears.jpg"
                >
                    <v-card-title>How to use the settings</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Each training session a student checks into accrues on the student's attendance report.
                    <br/>
                    You can set how many training sessions are required to be eligible for promotions. You can also choose
                    whether <i>each class</i> counts towards accrual or <i>one day's</i> training (could be two-a-days for some students)
                    count as one session towards accrual on their attendance report.
                    <br/>
                    Determine how many absences should pass until a student is contacted via email.
                    <br/>
                    <br/>
                    To set these settings for each belt group:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemSettings.png"
                                alt="Schedule"
                            />
                            menu item in the left side navigation menu.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Choose the appropriate belt group that you want to change settings for.
                            <br/>
                            <br/>
                            Determine whether <i>multiple checkins</i> during a day accrue <i> as one session</i> towards promotion
                            <v-img
                                class="ma-4"
                                width="361px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/settingAccrual2.png"
                                alt="class"
                            />
                            or whether <i>one class</i> a student checks into counts as one <i>session</i> towards promotion
                            <v-img
                                class="ma-4"
                                width="302px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/settingAccrual1.png"
                                alt="class"
                            />.
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>
<script>
export default {
    name: 'HelpSettings'
}
</script>
