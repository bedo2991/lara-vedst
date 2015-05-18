<!-- Needs variables: 'events', 'schedules',  'date', 'tasks', 'entries', 'weekStart', 'weekEnd', 'persons', 'clubs' -->

@extends('layouts.master')

@section('title')
	{{ utf8_encode(strftime("%d. %b", strtotime($weekStart))) }} - {{ utf8_encode(strftime("%d. %b", strtotime($weekEnd))) }}
@stop

@section('content')

@if(Session::has('userId'))
	<div class="row">
	<!-- create button -->
	    <div class="col-xs-12 col-md-3">
	        @if(Session::has('userGroup')
	            AND (Session::get('userGroup') == 'marketing'
	            OR Session::get('userGroup') == 'clubleitung'))
	            <a href="{{ Request::getBasePath() }}/calendar/create" class="btn btn-primary pull-left hidden-print">Neue Veranstaltung erstellen</a>
	        @endif
	    </div>

	<!-- prev/next week -->
	    <div class="col-xs-12 col-md-6">
			<ul class="pager" >
				<li class="col-xs-12 col-md-1"><a href="{{ Request::getBasePath() }}/calendar/{{$date['previousWeek']}}" class="hidden-print">&lt;&lt;</a></li>
				<li class="col-xs-12 col-md-10"><h5 style="display: inline;">&nbsp;&nbsp;&nbsp;&nbsp;{{ Config::get('messages_de.week-name') . $date['week']}}: 
				{{ utf8_encode(strftime("%d. %B", strtotime($weekStart))) }} - {{ utf8_encode(strftime("%d. %B", strtotime($weekEnd))) }}&nbsp;&nbsp;&nbsp;&nbsp;</h5></li>
				<li class="col-xs-12 col-md-1"><a href="{{ Request::getBasePath() }}/calendar/{{$date['nextWeek']}}" class="hidden-print">&gt;&gt;</a></li>
			</ul>
	    </div>

	<!-- club filtering -->
	    <div class="col-xs-12 col-md-3 hidden-print">
	        @include('partials.filter')
	    </div>
	</div>

	{!! Form::open([ 'route' => ['bulkUpdate', $date['year'], $date['week'] ] ]) !!}	

	@if (count($events)>0)
		{!! Form::submit('Änderungen speichern', array('class'=>'btn btn-success hidden-print')) !!}

		<div class="day-row">
			<div class="day-container">
				@foreach($events as $clubEvent)
					@if($clubEvent->evnt_is_private)
						@if(Session::has('userId'))
							<div class="inline-block {{ $clubEvent->getPlace->plc_title }}">
								@include('partials.weekCell', $clubEvent)
							</div>
						@endif
					@else
						<div class="inline-block {{ $clubEvent->getPlace->plc_title }}">
							@include('partials.weekCell', $clubEvent)
						</div>
					@endif
				@endforeach 

				<!-- whitespace between -->
				&nbsp;&nbsp;&nbsp;&nbsp;
				<!-- end of whitespace -->

				@foreach($tasks as $task)
						<div class="inline-block">
							@include('partials.taskWeekCell', $task)
						</div>
				@endforeach 
				
				<!-- whitespace on the far right -->
				<span class="hidden-print">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</span>
				<!-- end of whitespace -->
			</div>
		</div>

	@else
		<div class="panel">
			<div class="panel-heading">
				<h5>Keine Veranstaltungen diese Woche</h5>
			</div>
		</div>
	@endif

	{!! Form::close() !!}
@else
	<!-- Access for club members only -->
	@include('partials.accessDenied')
@endif

@stop


