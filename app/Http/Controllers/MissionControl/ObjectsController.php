<?php
namespace SpaceXStats\Http\Controllers\MissionControl;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use SpaceXStats\Http\Controllers\Controller;
use SpaceXStats\Http\Requests\EditObjectRequest;
use SpaceXStats\Library\Enums\MissionControlType;
use SpaceXStats\Library\Enums\ObjectPublicationStatus;
use SpaceXStats\Library\Enums\VisibilityStatus;
use SpaceXStats\Models\Download;
use SpaceXStats\Models\Favorite;
use SpaceXStats\Models\Note;
use SpaceXStats\Models\Object;
use JavaScript;
use SpaceXStats\Models\ObjectRevision;

class ObjectsController extends Controller {

    // GET
    // missioncontrol/objects/{object_id}
    public function get($object_id) {
        $object = Object::findOrFail($object_id);

        // Item has been viewed, increment!
        $object->incrementViewCounter();

        // Determine what type of object it is to show the correct view
        $viewType = strtolower(MissionControlType::getKey($object->type));

        // Object is visible to everyone and is published
        if ($object->visibility == VisibilityStatus::PublicStatus && $object->status == ObjectPublicationStatus::PublishedStatus) {

            if (Auth::isSubscriber()) {
                JavaScript::put([
                    'totalFavorites' => $object->favorites()->count(),
                    'isFavorited' => Auth::user()->favorites()->where('object_id', $object_id)->first(),
                    'userNote' => Auth::user()->notes()->where('object_id', $object_id)->first(),
                    'object' => $object
                ]);
            }

            return view('missionControl.objects.' . $viewType , ['object' => $object]);

        // Object is visible to subscribers, is published, and the logged in user is also a subscriber
        // or the user is an admin
        } elseif (($object->visibility == VisibilityStatus::DefaultStatus && $object->status == ObjectPublicationStatus::PublishedStatus && Auth::isSubscriber()) || Auth::isAdmin()) {

                // Inject dynamic data into page
                JavaScript::put([
                    'totalFavorites' => $object->favorites()->count(),
                    'isFavorited' => Auth::user()->favorites()->where('object_id', $object_id)->first(),
                    'userNote' => Auth::user()->notes()->where('object_id', $object_id)->first(),
                    'object' => $object
                ]);

                return view('missionControl.objects.' . $viewType , ['object' => $object]);
        }

        return App::abort(401);
    }

    /**
     * GET/PATCH, /missioncontrol/objects/{objectId}/edit. Allows for the editing of objects by mission
     * control subscribers and admins.
     *
     * @param $objectId
     *
     * @return \Illuminate\View\View
     */
    public function getEdit($objectId) {
        $object = Object::findOrFail($objectId);

        JavaScript::put([
           'object' => $object,
           'revisions' => $object->objectRevisions
        ]);

        return view('missionControl.objects.edit.edit', ['object' => $object]);
    }

    public function patchEdit(EditObjectRequest $request, $objectId) {
        $object = Object::findOrFail($objectId);

        DB::transation(function() use($objectId, $object) {
            // Create a snapshot of the object at this point in time
            ObjectRevision::create(array(
                'object_id' => $objectId,
                'user_id' => Auth::id(),
                'object' => $object->toJson(),
                'changelog' => Input::get('metadata.changelog'),
                'did_file_change' => false
            ));

            // Update the object
            $object->title = Input::get('object.title');
            $object->summary = Input::get('object.summary');
            $object->author = Input::get('object.author');
            $object->attribution = Input::get('object.attribution');

            $object->save();
        });

        return response()->json(null, 204);
    }

    /**
     * POST, /missioncontrol/objects/{objectId}/revert/{objectRevisionId}.
     *
     * @param $objectId
     * @param $objectRevisionId
     */
    public function revert($objectId, $objectRevisionId) {

    }

    public function addToCollection($objectId, $collectionId) {

    }

    // AJAX POST/PATCH/DELETE
    // missioncontrol/objects/{object_id}/note
    public function note($object_id) {
        if (Auth::isSubscriber()) {

            // Create
            if (request()->isMethod('post')) {
                $usernote = Note::create(array(
                    'user_id' => Auth::id(),
                    'object_id' => $object_id,
                    'note' => Input::get('note', null)
                ));
                $usernote->note = Input::get('note', null);
                $usernote->save();

            // Edit
            } elseif (request()->isMethod('patch')) {
                $usernote = Auth::user()->notes()->where('object_id', $object_id)->firstOrFail();
                $usernote->note = Input::get('note', null);
                Auth::user()->notes()->save($usernote);

            // Delete
            } elseif (request()->isMethod('delete')) {
                $usernote = Auth::user()->notes()->where('object_id', $object_id)->firstOrFail();
                $usernote->delete();
            }
            return response()->json(null, 204);
        }
        return response()->json(false, 401);
    }

    // AJAX POST/DELETE
    // missioncontrol/objects/{object_id}/favorite
    public function favorite($object_id) {
        if (Auth::isSubscriber()) {

            // Create Favorite
            if (request()->isMethod('post')) {

                if (Auth::user()->favorites()->count() == 0) {
                    Favorite::create(array(
                        'object_id' => $object_id,
                        'user_id' => Auth::id()
                    ));
                }

            // Delete Favorite
            } elseif (request()->isMethod('delete')) {
                Auth::user()->favorites()->where('object_id', $object_id)->firstOrFail()->delete();
            }

            return response()->json(null, 204);
        }

        return response()->json(false, 401);
    }

    // AJAX POST
    // missioncontrol/object/{object_id}/download
    public function download($object_id) {

        // Only increment the downloads table if the same user has not downloaded it in the last hour (just like views)
        $mostRecentDownload = Download::where('user_id', Auth::id())->where('object_id', $object_id)->first();

        if ($mostRecentDownload === null || $mostRecentDownload->created_at->diffInSeconds(Carbon::now()) > 3600) {
            Download::create(array(
                'user_id' => Auth::id(),
                'object_id' => $object_id
            ));
        }
        return response()->json(null, 204);
    }
}