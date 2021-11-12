<template>
    <v-container fluid>
        <v-row v-if="isAdmin(user) || isSuperAdmin(user)" justify="center">

            <v-card class="ma-12" style="width: 50%">

                <v-card-title>Upload Document Templates</v-card-title>

                <v-card-text>
                    Upload a new document template. This can be an agreement, disclosure or anything else you would
                    like to have your students sign.

                    <v-row class="my-10" justify="space-between" align="baseline">
                        <v-col cols="5">
                            <v-file-input
                                name="template"
                                v-model="file"
                                label="Select file"
                                :disabled="loading"
                                accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf"
                                :error="error"
                                :error-messages="error"
                            />
                        </v-col>
                        <v-col>
                            <v-btn title="Upload" fab small color="secondary" @click="upload" :disabled="uploading">
                                <v-icon>mdi-cloud-upload-outline</v-icon>
                            </v-btn>
                        </v-col>
                        <v-col cols="5">
                            <v-text-field
                                v-model="templateSearch"
                                append-icon="search"
                                label="Search"
                                single-line
                                hide-details
                            />
                        </v-col>
                    </v-row>


                    <v-data-table
                        :headers="templateHeaders"
                        :items="templates"
                        class="elevation-1"
                        :loading="loading"
                        :search="templateSearch"
                    >
                        <template v-slot:item.original_name="{ item }">
                            <a @click="downloadTemplate(item.id)">{{item.original_name}}</a>
                        </template>

                        <template v-slot:item.action="{ item }">
                            <v-icon small class="mr-2" @click="selectTemplate(item.template_id)" title="Send contract">
                                email
                            </v-icon>
                            <v-icon small @click="delDocument(item)" title="Delete">delete</v-icon>
                        </template>

                    </v-data-table>
                </v-card-text>

            </v-card>

            <v-dialog
                v-model="showSendDialog"
                max-width="500px"
            >
                <v-card class="pa-7">

                    <v-card-title>Send document out for signature</v-card-title>

                    <v-row justify="start" align="baseline">
                        <v-col cols="6">
                            <v-card-text>
                                <v-select
                                    v-model="to.userId"
                                    :items="users"
                                    label="Send to"
                                    item-value="id"
                                    item-text="name"
                                ></v-select>
                            </v-card-text>
                        </v-col>
                        <v-col>
                            <v-checkbox label="email" v-model="to.email" />
                        </v-col>
                        <v-col>
                            <v-checkbox label="phone" v-model="to.phone" />
                        </v-col>

                    </v-row>

                    <v-row justify="end">
                        <v-card-actions>
                            <v-btn text @click="sendContract" :disabled="sendable" tile="Send contract">Send</v-btn>
                            <v-btn color="primary" text @click="closeSend" title="Close">Close</v-btn>
                        </v-card-actions>
                    </v-row>
                </v-card>
            </v-dialog>
        </v-row>

        <v-row justify="center">
            <v-card>

                <v-card-title>
                    Documents
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="docSearch"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>

                <v-card-text>
                    <v-data-table
                        :headers="documentHeaders"
                        :items="documents"
                        class="elevation-1"
                        :loading="loading"
                        :search="docSearch"
                    >
                        <template v-slot:item.original_name="{ item }">
                            <span v-if=" ! item.contract_pdf_url">{{item.original_name}}</span>
                            <a v-else :href="item.contract_pdf_url" target="_blank">{{item.original_name}}</a>
                        </template>

                    </v-data-table>
                </v-card-text>

            </v-card>
        </v-row>
    </v-container>
</template>

<script>
import {headers, isAdmin, isSuperAdmin} from "../authorization";
import {utcDateTimeToLocal} from "../datetime_converters";

export default {
    name: "Documents",
    data: () => ({
        error: null,
        documentHeaders: [
            { text: 'Person', align: 'left', value: 'user.name' },
            { text: 'Name', align: 'left', value: 'original_name' },
            { text: 'Status', align: 'left', value: 'status' },
            { text: 'Updated At', value: 'updated_at' },
        ],
        docSearch: '',
        file: null,
        templateHeaders: [
            { text: 'Name', align: 'left', value: 'original_name' },
            { text: 'Status', align: 'left', value: 'status' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        loading: false,
        isAdmin,
        isSuperAdmin,
        showSendDialog: false,
        templateSearch: '',
        to: {
            email: false,
            phone: false,
            userId: null,
            templateId: null,
        }
    }),
    computed: {
        documents() {
            return this.$store.state.documents
                .filter(document => document.contract_id)
                .map(document => ({...document, updated_at: utcDateTimeToLocal(document.updated_at)}));
        },
        sendable() {
            return ! this.to.userId || ( ! this.to.email && ! this.to.phone);
        },
        user() {
            return this.$store.state.user;
        },
        users() {
            return this.$store.state.people;
        },
        templates() {
            return this.$store.state.documents.filter(document => document.file_name);
        },
    },
    methods: {
        closeSend() {
            this.showSendDialog = false;
            this.to = {email: false, phone: false, userId: null, templateId: null};
            this.file = null;
        },
        delDocument(document) {
            ! this.loading && confirm('Are you sure you want to permanently delete this document?') &&
            fetch(`/document/${this.$store.state.user.client_id}/${document.id}`, {
                method: 'DELETE',
                headers,
                credentials: "same-origin",
            })
                .then( resp => resp.json())
                .then( json => {
                    if (json.errors) {
                        this.error = json.errors.template;
                    } else if (json.exception) {
                        this.error = json.message;
                    } else {
                        this.error = null;
                        this.refresh();
                    }
                });
        },
        downloadTemplate(docId) {
            fetch(`/document/download/${this.user.client_id}/${docId}`, {
                headers,
                credentials: "same-origin",
            })
                .then(resp => resp.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    window.open(link,'_blank');
                });
        },
        async refresh() {
            this.loading = true;
            await this.$store.dispatch('getDocuments');
            this.loading = false;
        },
        selectTemplate(template_id) {
            const template = this.templates.find(template => template.template_id === template_id);

            if (template.status === 'processing') {

                alert("This template is still being processed. You'll be able to send it out for signature once it's in 'ready' status.");
                return;
            }
            this.to.templateId = template.id;
            this.showSendDialog = true;
        },
        sendContract() {
            this.error = null;
            fetch(`/document/${this.to.templateId}/${this.to.userId}`, {
                method: 'PUT',
                headers,
                credentials: "same-origin",
                body: JSON.stringify(this.to),
            })
                .then(resp => resp.json())
                .then( json => {
                    if ( ! json.success) {
                        this.error = json.error;
                    } else {
                        this.refresh();
                    }
                })
                .finally(() => {
                    this.closeSend();
                });
        },
        upload() {
            if ( ! this.file) {
                return;
            }
            this.loading = true;
            this.error = null;
            const formData = new FormData();
            formData.append('template', this.file);
            fetch('/document', {
                method: 'POST',
                headers: {"X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": CSRFToken,},
                credentials: "same-origin",
                body: formData,
            })
                .then( resp => resp.json())
                .then( json => {
                    if (json.errors) {
                        this.error = json.errors.template;
                    } else if (json.exception) {
                        this.error = json.message;
                    } else {
                        this.refresh();
                    }
                })
                .finally(() => {
                    this.loading = false;
                    this.file = null;
                });
        }
    },
    created() {
        this.refresh();
    }
}
</script>
