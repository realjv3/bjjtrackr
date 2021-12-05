<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="people-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/waiver-1024x683.png"
                >
                    <v-card-title style="color: #4b5054">How to manage documents</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Upload documents, waivers, forms, etc. as resendable templates for your students to fill out and sign.
                    Signature requests can be sent to a student's email or phone.
                    Signed documents are stored securely and can be downloaded at any time.
                    <br/><br/>
                    To upload a resendable template:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/documents.png"
                                alt="Documents"
                            />
                            menu item in the left side navigation menu to load documents page.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Upload a document you intend to send out for signatures. It will be saved as a template to be
                            reused indefinitely.
                            <v-img
                                class="ma-2"
                                src="/storage/img/template_select.png"
                                alt="Template upload"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Upon upload, the template will be in <i>processing</i> status. You will be notified when
                            template is done processing and ready to be sent out. Click on template name to view the template.
                            Hit the trash can icon to delete the template.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/template_table.png"
                                alt="Template table"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To send documents out for signature:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the envelope icon button under <i>Actions</i> for the template you want to send out
                            for signature.
                            <v-img
                                class="ma-2"
                                src="/storage/img/template_table.png"
                                alt="Template table"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Choose a recipient and whether to send to their phone or email, then hit the <i>Send</i> button.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/document_send.png"
                                alt="Document send dialog"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            The recipient will receive a message prompting them to view and sign the document on their
                            phone or email. The document will appear in the table with <i>sent</i> status. Once the recipient
                            opens it, the status will change to <i>viewed</i>, then to <i>signed</i> or <i>declined</i>.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/documentsList.png"
                                alt="Documents list"
                            />
                            Click on a signed document's name to view or download it.
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>
<script>
export default {
    name: 'HelpDocuments'
}
</script>
