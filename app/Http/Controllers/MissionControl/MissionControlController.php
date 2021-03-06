<?php
namespace SpaceXStats\Http\Controllers\MissionControl;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use SpaceXStats\Http\Controllers\Controller;
use SpaceXStats\Library\Enums\MissionControlSubtype;
use SpaceXStats\Library\Enums\MissionControlType;
use Carbon\Carbon;
use JavaScript;
use SpaceXStats\Models\Comment;
use SpaceXStats\Models\Download;
use SpaceXStats\Models\Favorite;
use SpaceXStats\Models\Mission;
use SpaceXStats\Models\Object;
use SpaceXStats\Models\User;

class MissionControlController extends Controller {

    /**
     * GET (HTTP). Home.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function home() {
		if (Auth::isSubscriber()) {

            JavaScript::put([
                'missions' => Mission::all()
            ]);

            return view('missionControl.home', [
                'upcomingMission' => Mission::future()->first()
            ]);
		} else {
            return redirect('/missioncontrol/about');
		}
	}

    public function about() {
        return view('missionControl.about', [
            'stripePublicKey' => Config::get('services.stripe.public')
        ]);
    }

    /**
     * GET (AJAX). Returns data for the homepage (relating to recent uploads, leaderboards, favorites, downloads, etc)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch() {
        // Uploads
        $uploads['latest'] = Object::authedVisibility()->inMissionControl()->orderBy('actioned_at')->take(10)->get();

        $uploads['hot'] = Object::authedVisibility()->inMissionControl()
            ->selectRaw('objects.*, LOG10(greatest(1, count(comments.object_id)) + greatest(1, count(favorites.object_id))) / TIMESTAMPDIFF(HOUR, objects.actioned_at, NOW()) as score')
            ->leftJoin('comments', 'comments.object_id', '=', 'objects.object_id')
            ->leftJoin('favorites', 'favorites.object_id', '=', 'objects.object_id')
            ->groupBy('objects.object_id')
            ->orderBy(DB::raw('score'))
            ->take(10)->get();

        $uploads['posts'] = Object::authedVisibility()->inMissionControl()->where('type', MissionControlType::Text)
            ->join('comments', 'comments.object_id', '=', 'objects.object_id')
            ->orderBy('comments.created_at')
            ->select('objects.*')
            ->take(10)
            ->get();

        $uploads['upcomingMission'] = Object::authedVisibility()->inMissionControl()->whereHas('Mission', function($q) {
            $q->future()->take(1);
        })->take(10)->get();

        $uploads['random'] = Object::authedVisibility()->inMissionControl()->orderByRaw("RAND()")->take(10)->get();

        // Leaderboards
        $leaderboards['week'] = User::join('awards', 'awards.user_id', '=', 'users.user_id')
            ->selectRaw('users.user_id, users.username, sum(awards.value) as totalDeltaV')
            ->where('awards.created_at', '>=', Carbon::now()->subWeek())
            ->groupBy('users.user_id')
            ->take(10)->get();

        $leaderboards['month'] = User::join('awards', 'awards.user_id', '=', 'users.user_id')
            ->selectRaw('users.user_id, users.username, sum(awards.value) as totalDeltaV')
            ->where('awards.created_at', '>=', Carbon::now()->subMonth())
            ->groupBy('users.user_id')
            ->take(10)->get();

        $leaderboards['year'] = User::join('awards', 'awards.user_id', '=', 'users.user_id')
            ->selectRaw('users.user_id, users.username, sum(awards.value) as totalDeltaV')
            ->where('awards.created_at', '>=', Carbon::now()->subYear())
            ->groupBy('users.user_id')
            ->take(10)->get();

        $leaderboards['alltime'] = User::join('awards', 'awards.user_id', '=', 'users.user_id')
            ->selectRaw('users.user_id, users.username, sum(awards.value) as totalDeltaV')
            ->groupBy('users.user_id')
            ->take(10)->get();

        // Comments
        $comments = Comment::with(['object' => function($query) {
            $query->select('object_id', 'title');
        }])
            ->with(['user' => function($query) {
            $query->select('user_id', 'username');
        }])
            ->orderBy('created_at','DESC')
            ->take(10)->get();

        // Favorites
        $favorites = Favorite::with(['object' => function($query) {
            $query->select('object_id', 'title');
        }])
            ->with(['user' => function($query) {
            $query->select('user_id', 'username');
        }])
            ->orderBy('created_at','DESC')
            ->take(10)->get();

        // Downloads
        $downloads = Download::with(['object' => function($query) {
            $query->select('object_id', 'title');
        }])
            ->with(['user' => function($query) {
                $query->select('user_id', 'username');
            }])
            ->orderBy('created_at','DESC')
            ->take(10)->get();

        return response()->json(array(
            'leaderboards' => $leaderboards,
            'uploads' => $uploads,
            'comments' => $comments,
            'favorites' => $favorites,
            'downloads' => $downloads
        ));
    }

    /**
     *
     */
    public function leaderboards() {
        
    }
}