<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index ()
    {
        try {
            $User = auth()->user();
            if ($User->role === 'Client') {
                // () Return tickets for the logged-in client
                $tickets = Ticket::where('user_id', $User->id)->get();
            } else {
                // () Return all users in JSON format
                $tickets = Ticket::all();
            }
            // : Return the tickets in JSON format
            return response()->json($tickets, 200);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    public function show ($id)
    {
        try {
            // () Search the ticket by ID
            $Ticket = Ticket::findOrFail($id);
            // () Verify that the ticket belongs to the authenticated user
            $User = auth()->user();
            if ($User->role === 'Client' && $Ticket->user_id !== $User->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to view this ticket.'
                ], 403);
            }
            // : Return the ticket in JSON format
            return response()->json($Ticket, 200);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found or an internal server error occurred.',
                'debug' => $Throwable->getMessage()
            ], 404);
        }
    }

    public function store (Request $Request)
    {
        try {
            // () Check if Collaborator is opening a ticket
            $User = auth()->user();
            if ($User->role === 'Collaborator') {
                return response()->json([
                    'status' => false,
                    'message' => 'Collaborators cannot open tickets (they can only answer tickets).'
                ], 403);
            }
            // () Valid User Input Request Data
            $validatedTicket = Validator::make($Request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'file' => 'file|mimes:pdf,docx,doc,jpg,png,jpeg|max:2048'
            ]);
            if ($validatedTicket->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request Input validation error',
                    'errors' => $validatedTicket->errors()
                ], 401);
            }

            // () Download Request File
            $file = $Request->file('file');
            if ($file !== null) {
                $path = $file->store('files');
            }

            // () Create Ticket
            $ticket = Ticket::create([
                'user_id' => $User->id,

                'title' => $Request->title,
                'description' => $Request->description,
                'file' => $path ?? null,

                'status' => 'opened',
            ]);
            // : Return Ticket created
            return response()->json($ticket, 201);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    public function close ($id)
    {
        try {
            $Ticket = Ticket::findOrFail($id);
            if ($Ticket->status != 'closed') {
                $Ticket->close();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Ticket already closed.'
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Ticket closed successfully!'
            ], 200);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                'debug' => $Throwable->getMessage()
            ], 500);
        }
    }
}
