<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\Episode;
use Illuminate\Support\Facades\Auth;

class EpisodePageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Episode $episode)
    {
        $course = $episode->course()->first();
        $chapter = $episode->chapter()->first();

        $student = Auth::user();

        // TODO: redo this as a query in Laravel
        $chaptersQuery = $course->chapters()->with('episodes')->get();
        $chapters = [];

        // TODO: move all of these into eduka cube
        $course->episode_count = 0;
        $course->chapter_count = count($chaptersQuery);
        $course->seen_episode_count = 0;
        $course->total_duration = 0;

        foreach ($chaptersQuery as $chapter) {
            $chapters[] = $chapter;

            $chapter->is_completed = true;
            $chapter->is_active = false;

            foreach ($chapter->episodes as $currentEpisode) {
                $course->episode_count += 1;
                $course->total_duration += $currentEpisode->duration;

                $currentEpisode->is_active = ($currentEpisode->id == $episode->id);
                $currentEpisode->is_seen = $student->isEpisodeSeen($currentEpisode);

                if (!$currentEpisode->is_seen)
                    $chapter->is_completed = false;

                if ($currentEpisode->is_active) {
                    $chapter->is_active = true;
                    $course->seen_episode_count += 1;
                }
            }

            $chapters[] = $chapter;
        }

        $course->completed_percent = (
            $course->episode_count == 0 ? 100
            : intval(100 * $course->seen_episode_count / $course->episode_count));

        $course->duration_for_humans = '3h 15m';

        // dd(Episode::where('id', $episode_uuid)->first());
        // Todo: check if they have access to the course and episode
        return view('backend::episode', [
            'episode' => $episode,
            'course' => $course,
            'chapter' => $chapter,
            'course_chapters' => $chapters
        ]);
    }
}
