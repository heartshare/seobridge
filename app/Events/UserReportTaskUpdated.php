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

class UserReportTaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $status;
    public $jobId;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $jobId, $status, $data = [])
    {
        $this->userId = $userId;
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
            'userId' => $this->userId,
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
        return new PrivateChannel('App.Models.UserReportTask.'.$this->userId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return ($this->status == 'page_added') ? 'page.add' : 'status.update';
    }
}
