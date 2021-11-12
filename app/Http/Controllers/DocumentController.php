<?php

namespace App\Http\Controllers;

use App\Client;
use App\Document;
use App\Mail\TemplateUploaded;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function read(Request $request,  int $clientId) {

        if (Gate::allows('isAdmin') || Gate::allows('isSuperAdmin')) {

            // get all for client
            return Document::with(['user'])->where('client_id', $clientId)->get();

        } else {
            // get all for user

            $user = Auth::user();

            return Document::with(['user'])
                ->where(['client_id' => $clientId, 'user_id' => $user->id])
                ->get();
        }
    }

    public function downloadTemplate(Request $request,  int $clientId, int $documentId) {

        $user = Auth::user();
        $document = Document::findOrFail($documentId);

        if (
            (Gate::allows('isAdmin') || Gate::allows('isSuperAdmin'))
            && (($document->user_id == $user->id) || Gate::allows('isSuperAdmin'))
        ) {
            return Storage::disk('spaces')->download($document->file_name);
        } else {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

    }

    public function create(Request $request) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin') ) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate(['template' => 'required|file']);

        $user = Auth::user();
        $client = Client::find($user->client_id);

        try {
            $fileName = $request->file('template')->store('templates', 'spaces');
            $document = Document::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'file_name' => $fileName,
                'original_name' => $request->file('template')->getClientOriginalName(),
            ]);

            Mail::to(config('mail.from.address'))->send(new TemplateUploaded($client, $user, $document));
        } catch (\Exception $exception) {

            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }

        return ['success' => true, 'fileName' => $fileName];
    }

    public function send(Request $request, int $documentId, int $userId) {

        try {
            $request->validate([
                'email' => 'boolean|required',
                'phone' => 'boolean|required',
            ]);

            $sender = Auth::user();
            $signer = User::findOrFail($userId);
            $document = Document::findOrFail($documentId);

            if (
                (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
                || $signer->client_id != $sender->client_id
                || $sender->client_id != $document->client_id
            ) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }

            if (empty($document->template_id)) {

                return response()->json(['error' => 'This template is still being processed.'], 404);
            }

            $signers = [
                [
                    'name' => $signer->name,
                ],
            ];
            if ($request->email) {
                $signers[0]['email'] = $signer->email;
            }
            if ($request->phone) {
                $signers[0]['mobile'] = $signer->phone;
            }
            $payload = [
                'template_id' => $document->template_id,
                'signers' => $signers,
                'custom_branding' => [
                    'company_name' => Client::find($sender->client_id)->name,
                ],
            ];
            if (config('app.env') != 'production') {
                $payload['test'] = 'yes';
            }
            $response = Http::withHeaders([
                'Content-type' => 'application/json'
            ])
                ->withBasicAuth(config('services.esignatures.token'), '')
                ->post("https://@esignatures.io/api/contracts", $payload);

            if ($response->failed()) {

                return response()->json(['success' => false, 'error' => $response->body()], $response->status());
            }
            $json = $response->json();
            Document::create([
                'client_id' => $signer->client_id,
                'user_id' => $signer->id,
                'original_name' => $json['data']['contract']['title'],
                'status' => $json['data']['contract']['status'],
                'contract_id' => $json['data']['contract']['id'],
            ]);

            return ['success' => true, $json];
        } catch (\Exception $exception) {

            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request,  int $clientId,  int $documentId) {

        $document = Document::findOrFail($documentId);
        $user = Auth::user();

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || $user->client_id != $clientId
            || $user->client_id != $document->client_id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        try{
            Storage::disk('spaces')->delete($document->file_name);
            $document->delete();

        } catch (\Exception $exception) {

            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }

        return ['success' => true, 'message' => 'Document deleted.'];
    }

    public function handle(Request $request) {

        try {
            $httpBasicAuthCreds = base64_decode(explode(' ', $_SERVER['HTTP_AUTHORIZATION'])[1]);

            if ($httpBasicAuthCreds != config('services.esignatures.token') . ':') {

                return response()->json(['error' => 'Unauthorized.'], 401);
            }
            $contractId = $request->input('data.contract.id') ?? $request->input('data.id');
            $contract = Document::where(['contract_id' => $contractId])->firstOrFail();

            switch ($request->input('status')) {

                case 'signer-viewed-the-contract':
                    $contract->status = 'viewed';
                    break;

                case 'contract-signed':
                    $contract->status = 'signed';
                    $contract->contract_pdf_url = str_replace('\u0026', '&', $request->input('data.contract_pdf_url'));
                    break;

                case 'signer-declined':
                    $contract->status = 'declined';
                    break;
            }
            $contract->save();

            return ['success' => true, 'response' => $request->all()];
        } catch (\Exception $exception) {

            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }
    }
}
