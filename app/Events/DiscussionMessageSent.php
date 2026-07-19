<?php

namespace App\Events;

use App\Models\DiscussionMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public DiscussionMessage $message)
    {
        $this->message->load('sender');
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('discussion.admin-comptable')];
    }

    public function broadcastAs(): string
    {
        return 'discussion.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_name' => trim($this->message->sender->prenom . ' ' . $this->message->sender->nom),
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}
