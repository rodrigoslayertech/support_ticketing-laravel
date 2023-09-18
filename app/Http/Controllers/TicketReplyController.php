<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TicketReplyController extends Controller
{
    public function index (Request $Request)
    {
        try {
            // () Validate Ticket id from Request params
            $validatedTicket = Validator::make($Request->all(), [
                'ticket' => 'required|int'
            ]);
            if ($validatedTicket->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request Input validation error',
                    'errors' => $validatedTicket->errors()
                ], 401);
            }
            // () Get Ticket id
            $ticket = $Request->input('ticket');
            // () Search the ticket by ID
            $Ticket = Ticket::findOrFail($ticket);

            // () Get User authenticated
            $User = auth()->user();
            // () Verify that the ticket belongs to the authenticated user
            if ($User->role === 'Client' && $Ticket && $Ticket->user_id !== $User->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to view this ticket.'
                ], 403);
            }

            $ticketsReplies = TicketReply::where('ticket_id', $ticket)->get();

            // : Return the tickets in JSON format
            return response()->json($ticketsReplies, 200);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    public function store (Request $Request)
    {
        try {
            $validatedTicketReply = Validator::make($Request->all(), [
                'ticket_id' => 'required|int',
                'reply' => 'required|string',
                'file' => 'file|mimes:pdf,docx,doc,jpg,png,jpeg|max:2048'
            ]);
            if ($validatedTicketReply->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request Input validation error',
                    'errors' => $validatedTicketReply->errors()
                ], 401);
            }
            // () Get Ticket id from request
            $ticket = (int) $Request->input('ticket_id');
            // () Search the ticket by ID
            $Ticket = Ticket::findOrFail($ticket);

            // () Store file if exists
            $file = $Request->file('file');
            if ($file !== null) {
                $path = $file->store('files');
            }
            // () Get User authenticated
            $User = auth()->user();
            // () Store new Ticket Reply
            $reply = new TicketReply([
                'ticket_id' => $Ticket->id,
                'user_id' => $User->id,
                'reply' => $Request->get('reply'),
                'file' => $path ?? null,
            ]);
            $reply->save();
            // : Return the ticket in JSON format
            return response()->json([
                'status' => true,
                'message' => 'Ticket reply saved successfully.'
            ], 200);
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
            return TicketReply::find($id);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    /*
    public function update(Request $request, TicketReply $ticketReply)
    {
        $ticketReply->update($request->all());

        return response()->json($ticketReply, 200);
    }
    */

    /*
    public function destroy(TicketReply $ticketReply)
    {
        $ticketReply->delete();

        return response()->json(null, 204);
    }
    */
}
