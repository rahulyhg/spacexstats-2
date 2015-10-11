<?php
namespace SpaceXStats\Models;

class ObjectRevision extends Model {
    protected $table = 'object_revisions';
    protected $primaryKey = 'object_revision_id';
    public $timestamps = true;

    protected $hidden = [];
    protected $appends = [];
    protected $fillable = [];
    protected $guarded = [];

    // Relationships
    public function object() {
        return $this->hasOne('Object');
    }

    public function user() {
        return $this->hasOne('User');
    }
}
