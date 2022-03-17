<template>
    <v-tour name="tour" :steps="steps" :callbacks="callbacks"/>
</template>

<script>
import {headers} from "../authorization";

export default {
    name: 'Tour',
    data () {
        return {
            callbacks: {
                onPreviousStep: this.onPrevious,
                onNextStep: this.onNext,
                onFinish: this.onFinish,
                onSkip: this.onFinish,
            },
            steps: [
                {
                    target: '.v-app-bar',
                    header: {title: 'Oss!'},
                    content: `Welcome to <strong>FlowRolled</strong>`,
                },
                {
                    target: '#speed-dial',
                    header: {title: 'Get Started'},
                    content: `First, input your students and staff.`,
                    params: {placement: 'left-start'},
                },
                {
                    target: '#speed-dial',
                    header: {title: 'Get Started'},
                    content: `Then, try setting up the class schedule.`,
                    params: {placement: 'left-start'},
                },
                {
                    target: '#speed-dial',
                    header: {title: 'Check-ins'},
                    content: `Students can checkin here.`,
                    params: {placement: 'left-start'},
                },
                {
                    target: '#qrcodes',
                    header: {title: 'Check-ins'},
                    content: `Or students can checkin via a QR code scan.`,
                },
                {
                    target: '#memberships',
                    header: {title: 'Membership Subscriptions'},
                    content: `Create membership plans and subscribe your students to them.`,
                },
                {
                    target: '#documents',
                    header: {title: 'Sign Waivers'},
                    content: `Upload waivers and other documents and send them to students for signature.`,
                },
                {
                    target: '#reports',
                    header: {title: 'Reports'},
                    content: `Here you can monitor students' progress and attendance.`,
                },
            ]
        }
    },
    methods: {
        onPrevious(currentStep) {
            this.$emit('step', {step: currentStep, dir: 'prev'});
        },
        onNext(currentStep) {
            this.$emit('step', {step: currentStep, dir: 'next'});
        },
        onFinish() {
            fetch(`/toured`, {headers, credentials: "same-origin"});
        },
    },
}
</script>
