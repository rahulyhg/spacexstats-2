<?php
namespace SpaceXStats\Creators\Objects;

use SpaceXStats\Creators\Creatable;
use SpaceXStats\Enums\MissionControlType;

class ObjectFromFile extends ObjectCreator implements Creatable {

    public function isValid($input) {
        $this->input = $input;

        switch ($input['type']) {
            case MissionControlType::Image:
                $rulesToGet = [];
                break;
            case MissionControlType::GIF:
                $rulesToGet = [];
                break;
            case MissionControlType::Video:
                $rulesToGet = [];
                break;
            case MissionControlType::Audio:
                $rulesToGet = [];
                break;
            case MissionControlType::Document:
                $rulesToGet = [];
                break;
        }

        $rules = array_intersect_key($this->object->getRules(), $rulesToGet);

        $validator = \Validator::make($input, $rules);

        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function create() {
        $this->object = \Object::find($this->input['object_id']);

        // Global object
        \DB::transaction(function() {
            $this->object->title = array_get($this->input, 'title', null);
            $this->object->summary = array_get($this->input, 'summary', null);
            $this->object->subtype = array_get($this->input, 'subtype', null);
            $this->object->originated_at = array_get($this->input, 'originated_at', null);
            $this->object->anonymous = array_get($this->input, 'anonymous', false);
            $this->object->attribution = array_get($this->input, 'attribution', null);
            $this->object->author = array_get($this->input, 'author', null);
            $this->object->status = 'Queued';

            // Set the mission relation if it exists
            $this->createMissionRelation();

            // Set the tag relations
            $this->createTagRelations();

            $this->object->save();
        });
    }
}