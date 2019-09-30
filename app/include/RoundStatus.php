<?php

class RoundStatus {
    public $round_num;
    public $status; // can be 'pending', 'started' or 'ended'
    
    public function __construct($round_num, $status) {
        $this->round_num = $round_num;
        $this->status = $status;
    }
}

?>