<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserReportTaskUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $status;
    public $jobId;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $jobId, $status, $data = [])
    {
        $this->user = $user;
        $this->jobId = $jobId;
        $this->status = $status;
        $this->data = $data;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'jobId' => $this->jobId,
            'status' => $this->status,
            'data' => $this->data,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Model.UserReportTask.'.$this->user->id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'status.update';
    }
}
