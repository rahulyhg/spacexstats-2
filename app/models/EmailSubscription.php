<?php
class EmailSubscription extends Eloquent {

    protected $table = 'email_subscriptions';
    protected $primaryKey = 'email_subscription_id';
    public $timestamps = false;

    protected $hidden = [];
    protected $appends = [];
    protected $fillable = [];
    protected $guarded = [];
}
