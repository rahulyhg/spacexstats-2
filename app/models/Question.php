<?php

class Question extends Eloquent {

	protected $table = 'questions';
	protected $primaryKey = 'question_id';
    public $timestamps = false;

    protected $hidden = [];
    protected $appends = [];
    protected $fillable = [];
    protected $guarded = [];

	// Relations
}