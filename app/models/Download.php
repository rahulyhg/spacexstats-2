<?php
class Download extends Eloquent {

    protected $table = 'downloads';
    protected $primaryKey = 'download_id';
    public $timestamps = true;

    protected $hidden = [];
    protected $appends = [];
    protected $fillable = [];
    protected $guarded = [];

    // Relations
    public function user() {
        return $this->belongsTo('User');
    }

    public function object() {
        return $this->belongsTo('Object');
    }
}