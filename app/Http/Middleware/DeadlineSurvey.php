<?php

namespace Lara\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Lara\utilities\RoleUtility;
use Redirect;
use Lara\Survey;

class DeadlineSurvey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $surveyId = intval($request->route()->parameter('survey'));
        /** @var Survey $survey */
        $survey = Survey::query()->findOrFail($surveyId);
        if(Carbon::now() < Carbon::parse($survey->deadline) && !is_null(\Auth::user())
            || \Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR)
            || \Auth::user()->hasPermissionsInSection($survey->section(),RoleUtility::PRIVILEGE_CL,RoleUtility::PRIVILEGE_MARKETING)) {
            return $next($request);
        } else {
            $request->session()->put('message', 'Die Deadline ist überschritten, jetzt können nurnoch Clubleitung/Marketing/Admin die Umfrage ausfüllen');
            $request->session()->put('msgType', 'danger');
            return Redirect::action('SurveyController@show', ['id' => $survey->id]);
        }
    }
}
