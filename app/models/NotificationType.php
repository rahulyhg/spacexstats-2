<?php
namespace SpaceXStats\Models;
class NotificationType extends Model {
    protected $table = 'notification_types';
    protected $primaryKey = 'notification_type_id';
    public $timestamps = false;

    protected $hidden = [];
    protected $appends = [];
    protected $fillable = [];
    protected $guarded = ['*'];

    public function notifications() {
        return $this->hasMany('Notification');
    }
}