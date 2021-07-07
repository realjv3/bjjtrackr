<template>
    <v-app>
        <v-main>
            <v-card class="mb-12">
                <v-card-title class="gray mb-2">Terms of Service</v-card-title>
                <v-card-text class="mb-12 pa-12">
                    <div class="text-body-2">
                        Please read these terms of service ("terms", "terms of service") carefully before using the flowrolled.com website (the "service") operated by flowrolled.com ("us", 'we", "our").
                        <br/>
                        This policy is effective as of 1 July 2021 and was last updated on 6 July 2021.
                    </div>
                    <br/>
                    <div class="text-h6">Conditions of Use</div>
                    <br/>
                    <div class="text-body-2">
                        We will provide their services to you, which are subject to the conditions stated below in this document. Every time you visit this website, use its services or make a purchase, you accept the following conditions. This is why we urge you to read them carefully.
                    </div>
                    <br/>
                    <div class="text-h6">Privacy Policy</div>
                    <br/>
                    <div class="text-body-2">
                        Before you continue using our website we advise you to read our privacy policy regarding our user data collection. It will help you better understand our practices.
                    </div>
                    <br/>
                    <div class="text-h6">Copyright</div>
                    <br/>
                    <div class="text-body-2">
                        Content published on this website (digital downloads, images, texts, graphics, logos) is the property of flowrolled.com and/or its content creators and protected by international copyright laws. The entire compilation of the content found on this website is the exclusive property of flowrolled.com, with copyright authorship for this compilation by flowrolled.com.
                    </div>
                    <br/>
                    <div class="text-h6">Communications</div>
                    <br/>
                    <div class="text-body-2">
                        The entire communication with us is electronic. Every time you send us an email or visit our website, you are going to be communicating with us. You hereby consent to receive communications from us. If you subscribe to the news on our website, you are going to receive regular emails from us. We will continue to communicate with you by posting news and notices on our website and by sending you emails. You also agree that all notices, disclosures, agreements and other communications we provide to you electronically meet the legal requirements that such communications be in writing.
                    </div>
                    <br/>
                    <div class="text-h6">Applicable Law</div>
                    <br/>
                    <div class="text-body-2">
                        By visiting this website, you agree that the laws of the [your location], without regard to principles of conflict laws, will govern these terms of service, or any dispute of any sort that might come between [name] and you, or its business partners and associates.
                    </div>
                    <br/>
                    <div class="text-h6">Disputes</div>
                    <br/>
                    <div class="text-body-2">
                        Any dispute related in any way to your visit to this website or to products you purchase from us shall be arbitrated by state or federal court and you consent to exclusive jurisdiction and venue of such courts.
                    </div>
                    <br/>
                    <div class="text-h6">Comments, Reviews, and Emails</div>
                    <br/>
                    <div class="text-body-2">
                        <p>
                            Visitors may post content as long as it is not obscene, illegal, defamatory, threatening, infringing of intellectual property rights, invasive of privacy or injurious in any other way to third parties. Content has to be free of software viruses, political campaign, and commercial solicitation.
                        </p>
                        <p>
                            We reserve all rights (but not the obligation) to remove and/or edit such content. When you post your content, you grant flowrolled.com non-exclusive, royalty-free and irrevocable right to use, reproduce, publish, modify such content throughout the world in any media.
                        </p>
                    </div>
                    <br/>
                    <div class="text-h6">License and Site Access</div>
                    <br/>
                    <div class="text-body-2">
                        We grant you a limited license to access and make personal use of this website. You are not allowed to download or modify it. This may be done only with written consent from us.
                    </div>
                    <br/>
                    <div class="text-h6">User Account</div>
                    <br/>
                    <div class="text-body-2">
                        <p>
                            If you are an owner of an account on this website, you are solely responsible for maintaining the confidentiality of your private user details (username and password). You are responsible for all activities that occur under your account or password.
                        </p>
                        <p>
                            We reserve all rights to terminate accounts, edit or remove content and cancel orders in their sole discretion.
                        </p>
                    </div>
                </v-card-text>

                <v-row justify="end" class="mx-2">
                    <v-card-actions>
                        <v-btn color="primary" @click="acceptToS" :disabled="saving">Accept</v-btn>
                    </v-card-actions>
                </v-row>
            </v-card>
        </v-main>
    </v-app>
</template>

<script>

import {headers} from "../authorization";
import Fetches from "../fetches";

const fetches = new Fetches();

export default {
    name: "ToS",
    data: function () {
        return {
            saving: false,
        };
    },
    methods: {
        async acceptToS() {
            this.saving = true;
            fetches.cancelFetches();
            const
                resp = await fetch(`/acceptToS`, {
                    headers,
                    credentials: "same-origin",
                    signal: fetches.getSignal(),
                });

            if (resp.status < 211) {
                this.saving = false;
                window.location = '/';
            }
        },
    },
}
</script>
